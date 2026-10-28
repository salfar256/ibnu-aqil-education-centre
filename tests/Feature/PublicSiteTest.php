<?php

namespace Tests\Feature;

use App\Models\{Article, Asset, ContentPage, School, Setting, User};
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
        foreach (['mi', 'smp', 'sma', 'smk', 'pesantren'] as $slug) {
            $this->page($slug, ['title' => strtoupper($slug).' Ibnu Aqil', 'type' => 'unit']);
        }
        $response = $this->get(route('public.home'))->assertOk();
        $response->assertSee('Ibnu Aqil Education Center')->assertDontSee('Portal Pengimbasan')->assertSee('id="primary-nav"', false)->assertSee('aria-controls="primary-nav"', false)->assertSee('Lewati ke konten utama');
        foreach (['mi', 'smp', 'sma', 'smk', 'pesantren'] as $slug) {
            $response->assertSee(route('public.unit', $slug), false);
            $this->get(route('public.unit', $slug))->assertOk()->assertSee(strtoupper($slug).' Ibnu Aqil');
        }
        $response->assertDontSee('/berita', false)->assertDontSee('/galeri', false)->assertSee(route('public.page', 'fasilitas'), false);
        $this->get('/berita')->assertNotFound();
        $this->get('/galeri')->assertNotFound();
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

    public function test_news_pages_are_not_published_even_for_published_articles(): void
    {
        $visible = $this->article('sudah-terbit');
        $this->get('/berita/'.$visible->slug)->assertNotFound();
        $this->get('/')->assertOk()->assertDontSee($visible->title);
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
        $response->assertSee(route('public.page', $page->slug), false)->assertDontSee($article->slug)->assertDontSee('/berita', false)->assertDontSee($draft->slug)->assertDontSee($unverified->slug)->assertDontSee('/portal')->assertDontSee('/admin')->assertDontSee('/berkas/');
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

    public function test_official_social_and_map_links_render_and_foreign_urls_are_rejected(): void
    {
        $this->page('kontak', ['title' => 'Mari terhubung']);
        $school = School::create(['name' => 'Lembaga Pengujian', 'code' => 'SOCIAL', 'is_active' => true]);
        $admin = User::create(['school_id' => $school->id, 'name' => 'Admin Uji', 'email' => 'social@example.test', 'password' => 'Correct!Password2026', 'role' => 'admin', 'is_active' => true, 'must_change_password' => false]);
        $valid = ['whatsapp_url' => 'https://wa.me/6285810183161', 'maps_url' => 'https://www.google.com/maps/search/?api=1&query=Ibnu+Aqil+Education+Centre', 'instagram_url' => 'https://www.instagram.com/ibnuaqilmedia', 'youtube_url' => 'https://www.youtube.com/@ibnuaqilmedia', 'tiktok_url' => 'https://www.tiktok.com/@ibnuaqilmedia', 'verified' => 1];

        $this->actingAs($admin)->post('/admin/manage/settings', ['instagram_url' => 'https://evil.example/ibnuaqilmedia'] + $valid)->assertSessionHasErrors('instagram_url');
        $this->post('/admin/manage/settings', ['tiktok_url' => 'https://www.tiktok.com/@ibnuaqilmedia?_r=1'] + $valid)->assertSessionHasErrors('tiktok_url');
        $this->post('/admin/manage/settings', ['maps_url' => 'https://maps.example.com/ibnu-aqil'] + $valid)->assertSessionHasErrors('maps_url');
        $this->post('/admin/manage/settings', $valid)->assertRedirect()->assertSessionHasNoErrors();

        $this->get(route('public.page', 'kontak'))->assertOk()
            ->assertSee('WhatsApp PPDB')->assertSee('https://wa.me/6285810183161', false)
            ->assertSee('https://www.instagram.com/ibnuaqilmedia', false)->assertSee('https://www.youtube.com/@ibnuaqilmedia', false)
            ->assertSee('https://www.tiktok.com/@ibnuaqilmedia', false)->assertSee('Petunjuk arah')->assertSee('Yuk, ikuti media sosial kami!')->assertDontSee('Cihideung');

        Setting::where('key', 'youtube_url')->update(['value' => 'https://evil.example/@ibnuaqilmedia']);
        $this->get(route('public.page', 'kontak'))->assertOk()->assertDontSee('evil.example');
    }

    public function test_brochure_showcase_renders_photos_achievements_and_admission_flow(): void
    {
        Setting::create(['key' => 'whatsapp_url', 'value' => 'https://wa.me/6285810183161']);
        Setting::create(['key' => 'maps_url', 'value' => 'https://share.google/1qFq51ITYcl8wqM9V']);
        $this->page('kontak');

        $this->get(route('public.home'))->assertOk()
            ->assertSee('assets/foto/web/masjid-hero.webp', false)->assertSee('assets/foto/web/kelas-tik.jpg', false)->assertSee('assets/foto/web/pembina-kh-agus-salim-hero.webp', false)->assertSee('assets/foto/web/ketua-yayasan.webp', false)->assertSee('Bismillahirrahmanirrahim')
            ->assertSee('Empat langkah')->assertSee('Chat WhatsApp PPDB')->assertSee('KH. Agus Salim')->assertSee('Hj. Saidah Hilmiyah, S.Pd., M.M.')->assertSee('Ketua Yayasan')
            ->assertSee('class="ia-bottom-nav"', false)->assertSee('https://share.google/1qFq51ITYcl8wqM9V', false)->assertSee('manifest.webmanifest', false);
        $this->get(route('public.achievements'))->assertOk()->assertSee('Juara 1 Pencak Silat tingkat Nasional')->assertDontSee('Belum ada prestasi');
        $this->page('fasilitas', ['body' => 'Daftar fasilitas terverifikasi.']);
        $this->get(route('public.page', 'fasilitas'))->assertOk()->assertSee('assets/foto/fasilitas/masjid-salapan-bentang.webp', false)->assertSee('Laboratorium komputer')->assertSee('Security 24 jam')->assertSee('assets/foto/fasilitas/tulisan-ibnu-aqil.webp', false)->assertSee('Mengaji bersama')->assertSee('Kegiatan MI Ibnu Aqil')->assertSee('assets/foto/kegiatan-siswa/mi-market-day.webp', false)->assertDontSee('assets/ekskul/', false)
            ->assertSee('Daftar fasilitas terverifikasi.');
        $this->get(route('public.page', 'kontak'))->assertOk()->assertSee('https://share.google/1qFq51ITYcl8wqM9V', false);
    }

    public function test_whatsapp_inquiries_are_prefilled_per_unit_and_portal_is_not_promoted(): void
    {
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
        $home = $this->get('/')->assertOk();
        $home->assertSee('https://wa.me/6285810183161?text=', false)
            ->assertDontSee('data-finder', false)
            ->assertSee('Yang sering')
            ->assertSee('Tanya rincian biaya')
            ->assertDontSee('Portal Pengimbasan')
            ->assertDontSee(route('portal.index'), false);
        $unit = $this->get('/unit/smk')->assertOk();
        $unit->assertSee(rawurlencode('pendaftaran di SMK Ibnu Aqil'), false)->assertDontSee('Portal Pengimbasan');
        $this->get('/unit/kbihu')->assertNotFound();
        $this->get('/')->assertDontSee('KBIHU')->assertSee('1992')->assertSee('30+');
    }

    public function test_unit_pages_show_leader_vision_and_optional_boarding_only_for_smp_and_sma(): void
    {
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
        $this->get('/unit/sma')->assertOk()->assertSee('Hidmatullah, S.E., M.Pd.')->assertSee('assets/foto/pimpinan/sma.webp', false)->assertSee('Visi kami')->assertDontSee('Misi')->assertSee('Ayo sambil mondok di pesantren!')->assertSee('assets/foto/siswa/sma-1.webp', false)->assertSee('assets/brosur/brosur-sma-2027-2028.pdf', false);
        $this->get('/unit/smp')->assertOk()->assertSee('Ade Irawan, S.Pd.I.')->assertSee('Ayo sambil mondok di pesantren!')->assertSee('assets/ekskul/formal/panahan.webp', false);
        $this->get('/unit/smk')->assertOk()->assertSee('Hj. Eva Fauziah, M.Pd.')->assertDontSee('Ayo sambil mondok')->assertSee('3 jurusan pilihan')->assertSee('Akuntansi &amp; Keuangan Lembaga', false);
        $this->get('/unit/mi')->assertOk()->assertSee('Dr. Hj. Fuzna Nur Aqilah, M.Pd.')->assertDontSee('Ayo sambil mondok')->assertSee('assets/ekskul/lucu/calistung.webp', false)->assertSee('assets/brosur/brosur-mi-2027-2028.pdf', false);
        $this->get('/unit/pesantren')->assertOk()->assertSee('Santri dari SMP &amp; SMA', false)->assertSee('Santri Pondok Pesantren Ibnu Aqil');
        $this->get('/tentang')->assertOk()->assertSee('KH. Agus Salim, S.Sos., M.M.')->assertSee('Menjaga tradisi yang baik')->assertSee('Sekolah Ibnu Aqil')->assertSee(route('public.unit', 'smk'), false);
        $this->get('/')->assertOk()->assertSee('ia-c-smk', false)->assertSee('Santri dari SMP &amp; SMA', false)->assertSee('assets/foto/web/masjid-hero.webp', false)->assertDontSee('Terhubung melalui ilmu')->assertSee('PPDB 2027/2028 · Sekolah Ibnu Aqil')->assertSee('Yayasan Haji Agus Salim Bogor')->assertSee('Kunjungi sekolah')->assertSee('MI, SMP, SMA, SMK');
        $this->get('/pendaftaran')->assertOk()->assertSee('Alur pendaftaran')->assertSee('assets/icons/daftar/isi-formulir.webp', false)->assertDontSee('Tanya admin</strong>', false)->assertSee('assets/foto/siswa/mi-1.webp', false)->assertSee('assets/brosur/brosur-smp-2027-2028.pdf', false);
    }
}
