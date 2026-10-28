<?php

namespace Tests\Feature;

use App\Models\{Activity, Article, Asset, ContentPage, Material, School, Setting, User};
use DOMDocument;
use DOMXPath;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\{File, Storage};
use Tests\TestCase;

class PublicExportTest extends TestCase
{
    use RefreshDatabase;

    private const URL = 'https://salfar256.github.io/ibnu-aqil-education-centre';
    private array $outputs = [];

    protected function tearDown(): void
    {
        foreach ($this->outputs as $path) {
            $expected = strtolower(str_replace('\\', '/', $path));
            $actual = strtolower(str_replace('\\', '/', (string) realpath($path)));
            if (is_dir($path) && $expected === $actual && str_starts_with(basename($path), 'test-public-export-')) {
                File::deleteDirectory($path);
            }
        }
        parent::tearDown();
    }

    private function exportDirectoryName(): string
    {
        $name = 'test-public-export-'.bin2hex(random_bytes(5));
        $this->outputs[] = base_path($name);

        return $name;
    }

    private function page(string $slug, array $attributes = []): ContentPage
    {
        return ContentPage::create(array_merge(['slug' => $slug, 'title' => 'Halaman '.$slug, 'summary' => 'Ringkasan publik uji.', 'body' => 'Isi publik yang sudah diverifikasi.', 'type' => 'page', 'status' => 'published', 'verified_at' => now()], $attributes));
    }

    private function article(string $slug, array $attributes = []): Article
    {
        return Article::create(array_merge(['slug' => $slug, 'title' => 'Kabar '.$slug, 'summary' => 'Ringkasan publik.', 'body' => 'Isi berita publik.', 'kind' => 'news', 'status' => 'published', 'verified_at' => now(), 'published_at' => now()->subHour()], $attributes));
    }

    public function test_export_only_contains_published_verified_public_content_and_referenced_images(): void
    {
        Storage::fake('private');
        $this->page('tentang');
        $this->page('mi', ['type' => 'unit', 'title' => 'MI Ibnu Aqil']);
        $this->page('catatan-publik');
        $this->page('fasilitas', ['status' => 'draft', 'body' => 'DRAF-TIDAK-BOLEH-TEREKSPOR']);
        $this->page('kontak', ['verified_at' => null, 'body' => 'BELUM-VERIFIKASI-TIDAK-BOLEH-TEREKSPOR']);
        $this->article('draf-berita', ['status' => 'draft', 'body' => 'RAHASIA-DRAF-BERITA']);
        $this->article('berita-masa-depan', ['published_at' => now()->addDay(), 'body' => 'RAHASIA-MASA-DEPAN']);
        $this->article('belum-verified', ['verified_at' => null, 'body' => 'RAHASIA-BELUM-VERIFIKASI']);

        $school = School::create(['name' => 'SEKOLAH-PRIVAT-RAHASIA', 'code' => 'SECRET', 'is_active' => true]);
        User::create(['school_id' => $school->id, 'name' => 'NAMA-AKUN-RAHASIA', 'email' => 'private-account@example.test', 'password' => 'NeverExport!2026', 'role' => 'partner', 'is_active' => true]);
        $activity = Activity::create(['title' => 'KEGIATAN-PORTAL-RAHASIA', 'slug' => 'kegiatan-privat', 'theme' => 'Privat', 'year' => 2026, 'status' => 'published']);
        $material = Material::create(['activity_id' => $activity->id, 'title' => 'MATERI-PORTAL-RAHASIA', 'slug' => 'materi-privat', 'description' => 'DESKRIPSI-PORTAL-RAHASIA', 'type' => 'pdf', 'author_name' => 'PENULIS-PRIVAT', 'status' => 'published', 'published_at' => now()]);
        Storage::disk('private')->put('media/private.pdf', '%PDF-1.4 ISI-BERKAS-PRIVAT');
        Asset::create(['material_id' => $material->id, 'original_name' => 'PRIVATE-FILE.pdf', 'path' => 'media/private.pdf', 'mime_type' => 'application/pdf', 'size' => 26, 'visibility' => 'private', 'allow_download' => true]);
        $photo = UploadedFile::fake()->image('foto.png', 20, 20);
        Storage::disk('private')->put('media/public-cover.png', file_get_contents($photo->getRealPath()));
        $cover = Asset::create(['original_name' => 'foto-publik.png', 'path' => 'media/public-cover.png', 'mime_type' => 'image/png', 'size' => $photo->getSize(), 'visibility' => 'public', 'allow_download' => true]);
        Storage::disk('private')->put('media/unused-cover.png', file_get_contents($photo->getRealPath()));
        $unused = Asset::create(['original_name' => 'foto-tidak-dipakai.png', 'path' => 'media/unused-cover.png', 'mime_type' => 'image/png', 'size' => $photo->getSize(), 'visibility' => 'public', 'allow_download' => true]);
        for ($index = 1; $index <= 13; $index++) {
            $this->article('publik-'.$index, ['cover_asset_id' => $index === 1 ? $cover->id : null]);
        }
        Setting::create(['key' => 'whatsapp_url', 'value' => 'https://wa.me/6281234567890']);
        $output = $this->exportDirectoryName();
        $this->artisan('app:export-public', ['--url' => self::URL, '--output' => $output])->assertExitCode(0);
        $root = base_path($output);

        foreach (['index.html', 'tentang/index.html', 'unit/mi/index.html', 'halaman/catatan-publik/index.html', 'prestasi/index.html', 'portal/index.html', '404.html', 'sitemap.xml', 'robots.txt', '.nojekyll', 'assets/site.css', 'assets/site.js', 'assets/ramah.css', 'assets/manifest.webmanifest'] as $relative) {
            $this->assertFileExists($root.'/'.$relative);
        }
        foreach (['fasilitas/index.html', 'kontak/index.html', 'berita/index.html', 'galeri/index.html', 'berita/publik-1/index.html', 'berita/draf-berita/index.html', 'berita/berita-masa-depan/index.html', 'berita/belum-verified/index.html', 'login/index.html', 'admin/index.html', 'storage/media/private.pdf', '.env', 'database/database.sqlite', 'media/'.$unused->id.'.png'] as $relative) {
            $this->assertFileDoesNotExist($root.'/'.$relative);
        }
        $contents = '';
        foreach (File::allFiles($root) as $file) {
            $this->assertNotSame('php', strtolower($file->getExtension()));
            if (in_array($file->getExtension(), ['html', 'xml', 'txt'], true)) {
                $contents .= file_get_contents($file->getPathname());
            }
        }
        foreach (['DRAF-TIDAK-BOLEH-TEREKSPOR', 'BELUM-VERIFIKASI-TIDAK-BOLEH-TEREKSPOR', 'RAHASIA-DRAF-BERITA', 'RAHASIA-MASA-DEPAN', 'RAHASIA-BELUM-VERIFIKASI', 'SEKOLAH-PRIVAT-RAHASIA', 'NAMA-AKUN-RAHASIA', 'private-account@example.test', 'KEGIATAN-PORTAL-RAHASIA', 'MATERI-PORTAL-RAHASIA', 'DESKRIPSI-PORTAL-RAHASIA', 'ISI-BERKAS-PRIVAT', 'PRIVATE-FILE.pdf', (string) config('app.key'), 'name="_token"', 'csrf-token', '<form'] as $secret) {
            $this->assertStringNotContainsString($secret, $contents);
        }
        $home = file_get_contents($root.'/index.html');
        $this->assertStringContainsString('href="'.self::URL.'/portal/"', $home);
        $this->assertStringContainsString('href="'.self::URL.'/unit/mi/"', $home);
        $this->assertStringContainsString('src="'.self::URL.'/assets/', $home);
        $this->assertStringNotContainsString('/berita/', $home);
        $this->assertStringNotContainsString('/galeri/', $home);
        $this->assertStringContainsString('https://wa.me/6281234567890', file_get_contents($root.'/portal/index.html'));
        $this->assertStringNotContainsString('/portal/', file_get_contents($root.'/sitemap.xml'));
        $this->crawlLocalLinks($root);
    }

    private function crawlLocalLinks(string $root): void
    {
        foreach (File::allFiles($root) as $file) {
            if ($file->getExtension() !== 'html') {
                continue;
            }
            $dom = new DOMDocument();
            $previous = libxml_use_internal_errors(true);
            $dom->loadHTML(file_get_contents($file->getPathname()), LIBXML_NONET);
            libxml_clear_errors();
            libxml_use_internal_errors($previous);
            $xpath = new DOMXPath($dom);
            foreach ($xpath->query('//*[@href or @src]') as $node) {
                foreach (['href', 'src'] as $attribute) {
                    if (!$node->hasAttribute($attribute)) {
                        continue;
                    }
                    $url = $node->getAttribute($attribute);
                    if (!str_starts_with($url, self::URL.'/')) {
                        $this->assertFalse(str_starts_with($url, 'http://localhost'));
                        continue;
                    }
                    $path = substr(parse_url($url, PHP_URL_PATH), strlen('/ibnu-aqil-education-centre/'));
                    $this->assertArrayNotHasKey('query', parse_url($url));
                    if ($path === '' || str_ends_with($path, '/')) {
                        $path .= 'index.html';
                    }
                    $this->assertFileExists($root.'/'.$path, 'Tautan lokal rusak: '.$url.' dalam '.$file->getRelativePathname());
                }
            }
        }
    }

    public function test_configured_portal_uses_real_https_address_without_rendering_login_form(): void
    {
        $output = $this->exportDirectoryName();
        $this->artisan('app:export-public', ['--url' => self::URL, '--output' => $output, '--portal-url' => 'https://portal.example.org/portal'])->assertExitCode(0);
        $html = file_get_contents(base_path($output).'/portal/index.html');
        $this->assertStringContainsString('href="https://portal.example.org/portal"', $html);
        $this->assertStringNotContainsString('<form', $html);
        $this->assertStringNotContainsString('name="password"', $html);
    }

    public function test_export_refuses_nonempty_protected_and_traversal_output_without_deleting_files(): void
    {
        $output = $this->exportDirectoryName();
        File::makeDirectory(base_path($output));
        file_put_contents(base_path($output).'/keep.txt', 'PRESERVE THIS FILE');
        $this->artisan('app:export-public', ['--output' => $output])->assertExitCode(1);
        $this->assertSame('PRESERVE THIS FILE', file_get_contents(base_path($output).'/keep.txt'));
        foreach (['public', 'storage', 'app', 'vendor', '../outside', '/tmp/export', 'C:\\outside'] as $path) {
            $this->artisan('app:export-public', ['--output' => $path])->assertExitCode(1);
        }
        $this->assertFileExists(base_path('artisan'));
        $this->assertFileExists(public_path('index.php'));
    }

    public function test_export_rejects_unsafe_base_and_portal_urls(): void
    {
        foreach (['http://example.org', 'javascript:alert(1)', 'https://user@example.org', 'https://example.org/path/../escape', 'https://example.org?token=secret'] as $url) {
            $this->artisan('app:export-public', ['--url' => $url, '--output' => $this->exportDirectoryName()])->assertExitCode(1);
        }
        $this->artisan('app:export-public', ['--output' => $this->exportDirectoryName(), '--portal-url' => 'javascript:alert(1)'])->assertExitCode(1);
    }
}
