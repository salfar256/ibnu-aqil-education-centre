<?php

namespace Tests\Feature;

use App\Models\{Article, Asset, ContentPage, School, User};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PublicSiteTest extends TestCase
{
    use RefreshDatabase;

    private function page(string $slug, array $attributes = []): ContentPage
    {
        return ContentPage::create(array_merge(['slug' => $slug, 'title' => 'Halaman '.$slug, 'summary' => 'Ringkasan terverifikasi untuk pengujian.', 'body' => 'Isi halaman untuk pengujian.', 'type' => 'page', 'status' => 'published', 'verified_at' => now()], $attributes));
    }

    private function article(string $slug, array $attributes = []): Article
    {
        return Article::create(array_merge(['slug' => $slug, 'title' => 'Berita '.$slug, 'summary' => 'Ringkasan berita pengujian.', 'body' => 'Isi berita pengujian.', 'kind' => 'news', 'status' => 'published', 'verified_at' => now(), 'published_at' => now()->subHour()], $attributes));
    }

    public function test_home_and_public_navigation_render_without_external_dependencies(): void
    {
        foreach (['mi', 'smp', 'sma', 'smk', 'pesantren', 'kbihu'] as $slug) {
            $this->page($slug, ['title' => strtoupper($slug).' Ibnu Aqil', 'type' => 'unit']);
        }
        $response = $this->get(route('public.home'))->assertOk();
        $response->assertSee('Ibnu Aqil Education Centre')->assertSee('Portal Pengimbasan')->assertSee('id="primary-nav"', false)->assertSee('aria-controls="primary-nav"', false)->assertSee('Lewati ke konten utama');
        foreach (['mi', 'smp', 'sma', 'smk', 'pesantren', 'kbihu'] as $slug) {
            $response->assertSee(route('public.unit', $slug), false);
            $this->get(route('public.unit', $slug))->assertOk()->assertSee(strtoupper($slug).' Ibnu Aqil');
        }
        $this->get(route('public.news'))->assertOk();
        $this->get(route('public.gallery'))->assertOk();
    }

    public function test_unverified_and_draft_public_pages_are_never_served(): void
    {
        $this->page('tentang', ['body' => 'Isi terverifikasi terlihat.']);
        $this->page('pendaftaran', ['status' => 'draft', 'body' => 'KONTEN DRAF RAHASIA']);
        $this->page('fasilitas', ['verified_at' => null, 'body' => 'KONTEN BELUM DIVERIFIKASI']);

        $this->get(route('public.page', 'tentang'))->assertOk()->assertSee('Isi terverifikasi terlihat.');
        $this->get(route('public.page', 'pendaftaran'))->assertNotFound();
        $this->get(route('public.page', 'fasilitas'))->assertNotFound();
        $this->get('/')->assertOk()->assertDontSee('KONTEN DRAF RAHASIA')->assertDontSee('KONTEN BELUM DIVERIFIKASI');
    }

    public function test_news_requires_verified_published_content_and_reached_publication_date(): void
    {
        $visible = $this->article('sudah-terbit');
        $hidden = [
            $this->article('draf-berita', ['status' => 'draft']),
            $this->article('berita-belum-diverifikasi', ['verified_at' => null]),
            $this->article('berita-masa-depan', ['published_at' => now()->addDay()]),
        ];

        $response = $this->get(route('public.news'))->assertOk()->assertSee($visible->title);
        $this->get(route('public.article', $visible->slug))->assertOk()->assertSee($visible->body);
        foreach ($hidden as $article) {
            $response->assertDontSee($article->title);
            $this->get(route('public.article', $article->slug))->assertNotFound();
        }
    }

    public function test_public_content_is_escaped_and_has_canonical_and_sharing_metadata(): void
    {
        $page = $this->page('tentang', ['title' => 'Tentang Ibnu Aqil', 'body' => '<script>alert("unsafe")</script>']);
        $response = $this->get(route('public.page', $page->slug))->assertOk();

        $response->assertSee('&lt;script&gt;', false)->assertDontSee('<script>alert("unsafe")</script>', false)->assertSee('<link rel="canonical" href="'.route('public.page', $page->slug).'">', false)->assertSee('property="og:title"', false)->assertSee('<html lang="id">', false);
    }

    public function test_public_media_cannot_expose_an_asset_marked_private(): void
    {
        Storage::fake('private');
        Storage::disk('private')->put('media/private.pdf', '%PDF-1.4 private');
        $private = Asset::create(['original_name' => 'private.pdf', 'path' => 'media/private.pdf', 'mime_type' => 'application/pdf', 'size' => 16, 'visibility' => 'private', 'allow_download' => true]);

        $this->get('/media/'.$private->id)->assertNotFound();
        $this->assertContains($this->get('/storage/media/private.pdf')->status(), [403, 404]);
    }

    public function test_sitemap_contains_verified_public_urls_and_excludes_private_surfaces(): void
    {
        $page = $this->page('tentang');
        $article = $this->article('kabar-terbit');
        $draft = $this->article('kabar-draf', ['status' => 'draft']);
        $unverified = $this->page('halaman-belum-pasti', ['verified_at' => null]);

        $response = $this->get('/sitemap.xml')->assertOk();
        $response->assertSee(route('public.page', $page->slug), false)->assertSee(route('public.article', $article->slug), false)->assertDontSee($draft->slug)->assertDontSee($unverified->slug)->assertDontSee('/portal')->assertDontSee('/admin')->assertDontSee('/berkas/');
        $this->get('/robots.txt')->assertOk()->assertSee('Disallow: /portal')->assertSee('Disallow: /admin')->assertSee('Disallow: /berkas');
    }

    public function test_admin_must_confirm_verification_before_publication(): void
    {
        $school = School::create(['name' => 'Lembaga Pengujian', 'code' => 'VERIFY', 'is_active' => true]);
        $admin = User::create(['school_id' => $school->id, 'name' => 'Admin Uji', 'email' => 'verify@example.test', 'password' => 'Correct!Password2026', 'role' => 'admin', 'is_active' => true, 'must_change_password' => false]);
        $payload = ['title' => 'Tentang lembaga uji', 'slug' => 'tentang', 'summary' => 'Ringkasan hasil verifikasi.', 'body' => 'Informasi terverifikasi.', 'type' => 'page', 'status' => 'published', 'source_note' => 'Sumber pengujian', 'verified' => 0];
        $this->actingAs($admin)->post('/admin/manage/pages', $payload)->assertRedirect()->assertSessionHasErrors('verified');
        $this->assertDatabaseMissing('content_pages', ['slug' => 'tentang']);

        $payload['verified'] = 1;
        $this->post('/admin/manage/pages', $payload)->assertRedirect()->assertSessionHasNoErrors();
        $this->assertNotNull(ContentPage::where('slug', 'tentang')->firstOrFail()->verified_at);
        $this->get(route('public.page', 'tentang'))->assertOk()->assertSee('Informasi terverifikasi.');
    }
}
