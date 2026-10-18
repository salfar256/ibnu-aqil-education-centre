<?php

namespace Tests\Feature;

use App\Models\{AccessGrant, Activity, Asset, Material, School, User};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\{Hash, Storage};
use Tests\TestCase;

class PortalSecurityTest extends TestCase
{
    use RefreshDatabase;

    private const PDF = "%PDF-1.4\n1 0 obj\n<< /Type /Catalog >>\nendobj\ntrailer\n<< /Root 1 0 R >>\n%%EOF";

    private function school(string $code): School
    {
        return School::create(['name' => 'Sekolah Uji '.$code, 'code' => $code, 'is_active' => true]);
    }

    private function user(School $school, string $role = 'partner'): User
    {
        return User::create(['school_id' => $school->id, 'name' => 'Pengguna Uji', 'email' => uniqid('uji-').'@example.test', 'password' => Hash::make('Correct!Password2026'), 'role' => $role, 'is_active' => true, 'must_change_password' => false]);
    }

    private function activity(string $slug, array $attributes = []): Activity
    {
        return Activity::create(array_merge(['title' => 'Kegiatan '.$slug, 'slug' => $slug, 'theme' => 'Praktik baik', 'year' => 2026, 'description' => 'Konten untuk pengujian akses.', 'status' => 'published'], $attributes));
    }

    private function material(Activity $activity, string $slug, array $attributes = []): Material
    {
        return Material::create(array_merge(['activity_id' => $activity->id, 'title' => 'Materi '.$slug, 'slug' => $slug, 'description' => 'Deskripsi materi pengujian.', 'type' => 'pdf', 'author_name' => 'Pembuat Uji', 'status' => 'published', 'published_at' => now()->subDay()], $attributes));
    }

    private function grant(School $school, Activity $activity, array $attributes = []): AccessGrant
    {
        return AccessGrant::create(array_merge(['school_id' => $school->id, 'activity_id' => $activity->id, 'is_active' => true, 'allow_download' => true], $attributes));
    }

    private function asset(Material $material, array $attributes = []): Asset
    {
        Storage::disk('private')->put('media/uji-'.$material->id.'.pdf', self::PDF);

        return Asset::create(array_merge(['material_id' => $material->id, 'original_name' => 'modul-uji.pdf', 'path' => 'media/uji-'.$material->id.'.pdf', 'mime_type' => 'application/pdf', 'size' => strlen(self::PDF), 'allow_download' => true, 'visibility' => 'private'], $attributes));
    }

    public function test_guest_cannot_open_portal_material_or_private_file(): void
    {
        Storage::fake('private');
        $material = $this->material($this->activity('privat'), 'rahasia');
        $asset = $this->asset($material);

        $this->get('/portal')->assertRedirect('/login');
        $this->get('/portal/materi/'.$material->id)->assertRedirect('/login');
        $this->get('/berkas/'.$asset->id)->assertRedirect('/login');
    }

    public function test_school_cannot_list_open_or_download_another_schools_material(): void
    {
        Storage::fake('private');
        $schoolA = $this->school('A');
        $schoolB = $this->school('B');
        $activityA = $this->activity('khusus-a');
        $activityB = $this->activity('khusus-b');
        $materialA = $this->material($activityA, 'rahasia-sekolah-a');
        $materialB = $this->material($activityB, 'rahasia-sekolah-b');
        $this->grant($schoolA, $activityA);
        $this->grant($schoolB, $activityB);
        $assetA = $this->asset($materialA);
        $assetB = $this->asset($materialB);
        $this->actingAs($this->user($schoolA));

        $this->get('/portal')->assertOk()->assertSee($materialA->title)->assertDontSee($materialB->title);
        $this->get('/portal/materi/'.$materialA->id)->assertOk();
        $response = $this->get('/berkas/'.$assetA->id)->assertOk()->assertHeader('Content-Type', 'application/pdf')->assertHeader('X-Robots-Tag', 'noindex, nofollow, noarchive');
        $this->assertStringContainsString('private', $response->headers->get('Cache-Control'));
        $this->assertStringContainsString('no-store', $response->headers->get('Cache-Control'));
        ob_start();
        $response->baseResponse->sendContent();
        $this->assertSame(self::PDF, ob_get_clean());
        $this->get('/portal/materi/'.$materialB->id)->assertNotFound();
        $this->get('/berkas/'.$assetB->id)->assertNotFound();
        $this->get('/berkas/'.$assetB->id.'?download=1')->assertNotFound();
        $this->get('/media/'.$assetB->id)->assertNotFound();
        $this->assertContains($this->get('/storage/'.$assetB->path)->status(), [403, 404]);
    }

    public function test_revocation_removes_material_and_file_access_immediately(): void
    {
        Storage::fake('private');
        $school = $this->school('REVOKE');
        $activity = $this->activity('cabut');
        $material = $this->material($activity, 'dicabut');
        $asset = $this->asset($material);
        $grant = $this->grant($school, $activity);
        $this->actingAs($this->user($school));
        $this->get('/berkas/'.$asset->id.'?download=1')->assertOk();

        $grant->update(['is_active' => false]);

        $this->get('/portal')->assertOk()->assertDontSee($material->title);
        $this->get('/portal/materi/'.$material->id)->assertNotFound();
        $this->get('/berkas/'.$asset->id)->assertNotFound();
        $this->get('/berkas/'.$asset->id.'?download=1')->assertNotFound();
    }

    public function test_expired_grant_is_denied_for_detail_and_file_urls(): void
    {
        Storage::fake('private');
        $school = $this->school('EXPIRED');
        $activity = $this->activity('kedaluwarsa');
        $material = $this->material($activity, 'lama');
        $asset = $this->asset($material);
        $this->grant($school, $activity, ['expires_at' => now()->subMinute()]);
        $this->actingAs($this->user($school));

        $this->get('/portal/materi/'.$material->id)->assertNotFound();
        $this->get('/berkas/'.$asset->id)->assertNotFound();
    }

    public function test_view_only_access_does_not_grant_a_download(): void
    {
        Storage::fake('private');
        $school = $this->school('VIEW');
        $activity = $this->activity('baca');
        $material = $this->material($activity, 'hanya-baca');
        $asset = $this->asset($material);
        $grant = $this->grant($school, $activity, ['allow_download' => false]);
        $this->actingAs($this->user($school));

        $this->get('/berkas/'.$asset->id)->assertOk();
        $this->get('/berkas/'.$asset->id.'?download=1')->assertForbidden();

        $grant->update(['allow_download' => true]);
        $this->get('/berkas/'.$asset->id.'?download=1')->assertOk()->assertHeader('Content-Disposition');

        $asset->update(['allow_download' => false]);
        $this->get('/berkas/'.$asset->id.'?download=1')->assertForbidden();
    }

    public function test_draft_future_and_unpublished_activity_materials_remain_private(): void
    {
        Storage::fake('private');
        $school = $this->school('DRAFT');
        $activity = $this->activity('terbit');
        $draftActivity = $this->activity('belum-terbit', ['status' => 'draft']);
        $this->grant($school, $activity);
        $this->grant($school, $draftActivity);
        $materials = [
            $this->material($activity, 'draf', ['status' => 'draft']),
            $this->material($activity, 'masa-depan', ['published_at' => now()->addDay()]),
            $this->material($draftActivity, 'kegiatan-draf'),
        ];
        $this->actingAs($this->user($school));

        foreach ($materials as $material) {
            $asset = $this->asset($material);
            $this->get('/portal')->assertOk()->assertDontSee($material->title);
            $this->get('/portal/materi/'.$material->id)->assertNotFound();
            $this->get('/berkas/'.$asset->id)->assertNotFound();
        }
    }

    public function test_portal_search_and_filters_preserve_school_scope(): void
    {
        $school = $this->school('FILTER');
        $activity = $this->activity('literasi', ['theme' => 'Literasi', 'year' => 2026]);
        $otherActivity = $this->activity('numerasi', ['theme' => 'Numerasi', 'year' => 2025]);
        $privateActivity = $this->activity('tanpa-izin', ['theme' => 'Literasi', 'year' => 2026]);
        $allowed = $this->material($activity, 'literasi-diizinkan', ['type' => 'module']);
        $other = $this->material($otherActivity, 'numerasi-diizinkan');
        $denied = $this->material($privateActivity, 'literasi-terlarang', ['type' => 'module']);
        $this->grant($school, $activity);
        $this->grant($school, $otherActivity);
        $this->actingAs($this->user($school));

        foreach ([['q' => 'literasi'], ['theme' => 'Literasi'], ['year' => 2026], ['type' => 'module'], ['activity' => $activity->id]] as $filter) {
            $this->get('/portal?'.http_build_query($filter))->assertOk()->assertSee($allowed->title)->assertDontSee($other->title)->assertDontSee($denied->title);
        }
    }

    public function test_editor_cannot_manage_accounts_public_content_or_escalate_own_role(): void
    {
        $editor = $this->user($this->school('EDITOR'), 'editor');
        $this->actingAs($editor);

        foreach (['schools', 'users', 'grants', 'pages', 'articles', 'settings', 'logs'] as $resource) {
            $this->get('/admin/manage/'.$resource)->assertForbidden();
        }
        $this->put('/admin/manage/users/'.$editor->id, ['name' => $editor->name, 'email' => $editor->email, 'school_id' => $editor->school_id, 'role' => 'admin', 'is_active' => 1])->assertForbidden();
        $this->assertSame('editor', $editor->fresh()->role);
        $this->post('/admin/manage/schools', ['name' => 'Tidak diizinkan', 'code' => 'ILLEGAL', 'is_active' => 1])->assertForbidden();
        $this->assertDatabaseMissing('schools', ['code' => 'ILLEGAL']);
        $this->get('/admin/manage/materials')->assertOk();
        $this->get('/admin/manage/activities')->assertOk();
    }

    public function test_partner_cannot_access_admin_or_upload_files(): void
    {
        $partner = $this->user($this->school('PARTNER'));
        $this->actingAs($partner);

        $this->get('/admin')->assertForbidden();
        $this->get('/admin/manage/materials')->assertForbidden();
        $this->post('/admin/assets', ['files' => [UploadedFile::fake()->createWithContent('uji.pdf', self::PDF)]])->assertForbidden();
        $this->assertDatabaseCount('assets', 0);
    }

    public function test_upload_accepts_safe_pdf_and_photo_and_stores_them_outside_public_disk(): void
    {
        Storage::fake('private');
        Storage::fake('public');
        $material = $this->material($this->activity('unggah'), 'uji-unggahan');
        $this->actingAs($this->user($this->school('UPLOAD'), 'editor'));

        $this->post('/admin/assets', ['material_id' => $material->id, 'allow_download' => 1, 'files' => [UploadedFile::fake()->createWithContent('modul.pdf', self::PDF), UploadedFile::fake()->image('foto.png', 2400, 1200)]])->assertRedirect()->assertSessionHasNoErrors();
        $this->assertDatabaseCount('assets', 2);
        foreach (Asset::all() as $asset) {
            $this->assertSame('private', $asset->visibility);
            $this->assertStringNotContainsString($asset->original_name, $asset->path);
            Storage::disk('private')->assertExists($asset->path);
            Storage::disk('public')->assertMissing($asset->path);
        }
        $image = Asset::where('mime_type', 'image/webp')->firstOrFail();
        $dimensions = getimagesize(Storage::disk('private')->path($image->path));
        $this->assertSame(1920, $dimensions[0]);
        $this->assertSame(960, $dimensions[1]);
    }

    public function test_upload_rejects_disguised_executable_and_active_pdf_without_partial_files(): void
    {
        Storage::fake('private');
        $material = $this->material($this->activity('validasi'), 'validasi-berkas');
        $this->actingAs($this->user($this->school('VALIDATE'), 'editor'));

        foreach ([UploadedFile::fake()->createWithContent('shell.php', self::PDF), UploadedFile::fake()->createWithContent('palsu.pdf', '<?php echo "unsafe";'), UploadedFile::fake()->createWithContent('aktif.pdf', "%PDF-1.4\n<< /OpenAction << /S /JavaScript /JS (alert(1)) >> >>\n%%EOF")] as $file) {
            $this->post('/admin/assets', ['material_id' => $material->id, 'files' => [$file]])->assertRedirect()->assertSessionHasErrors();
            $this->assertDatabaseCount('assets', 0);
            $this->assertSame([], Storage::disk('private')->allFiles());
        }
    }

    public function test_oversized_upload_and_failed_batch_leave_no_persisted_assets(): void
    {
        Storage::fake('private');
        config(['portal.upload_max_kb' => 1]);
        $material = $this->material($this->activity('batas'), 'batas-unggah');
        $this->actingAs($this->user($this->school('LIMIT'), 'editor'));

        $this->post('/admin/assets', ['material_id' => $material->id, 'files' => [UploadedFile::fake()->create('terlalu-besar.pdf', 2, 'application/pdf')]])->assertRedirect()->assertSessionHasErrors();
        $this->assertDatabaseCount('assets', 0);

        config(['portal.upload_max_kb' => 1024]);
        $this->post('/admin/assets', ['material_id' => $material->id, 'files' => [UploadedFile::fake()->createWithContent('aman.pdf', self::PDF), UploadedFile::fake()->createWithContent('aktif.pdf', "%PDF-1.4\n/JavaScript (unsafe)\n%%EOF")]])->assertRedirect()->assertSessionHasErrors();
        $this->assertDatabaseCount('assets', 0);
        $this->assertSame([], Storage::disk('private')->allFiles());
    }

    public function test_admin_can_create_account_and_disable_it_without_a_default_password(): void
    {
        $school = $this->school('MANAGE');
        $admin = $this->user($school, 'admin');
        $this->actingAs($admin);
        $this->post('/admin/manage/users', ['name' => 'Akun baru uji', 'email' => 'new-partner@example.test', 'school_id' => $school->id, 'role' => 'partner', 'is_active' => 1, 'send_reset' => 0])->assertRedirect()->assertSessionHasNoErrors();
        $partner = User::where('email', 'new-partner@example.test')->firstOrFail();
        $this->assertTrue($partner->must_change_password);
        $this->assertFalse(Hash::check('password', $partner->password));
        $this->delete('/admin/manage/users/'.$partner->id)->assertRedirect();
        $this->assertFalse($partner->fresh()->is_active);
        $this->actingAs($partner->fresh())->get('/portal')->assertForbidden();
    }

    public function test_admin_publication_and_grant_workflow_controls_partner_access(): void
    {
        $institution = $this->school('INSTITUTION');
        $school = $this->school('WORKFLOW');
        $admin = $this->user($institution, 'admin');
        $partner = $this->user($school);
        $this->actingAs($admin);
        $this->post('/admin/manage/activities', ['title' => 'Kegiatan alur uji', 'slug' => 'alur-uji', 'theme' => 'Literasi', 'year' => 2026, 'description' => 'Deskripsi kegiatan uji.', 'status' => 'published'])->assertRedirect()->assertSessionHasNoErrors();
        $activity = Activity::where('slug', 'alur-uji')->firstOrFail();
        $payload = ['activity_id' => $activity->id, 'title' => 'Materi alur uji', 'slug' => 'materi-alur-uji', 'description' => 'Isi materi uji.', 'type' => 'module', 'author_name' => 'Pengelola Uji', 'status' => 'draft', 'video_protection' => 'link'];
        $this->post('/admin/manage/materials', $payload)->assertRedirect()->assertSessionHasNoErrors();
        $material = Material::where('slug', 'materi-alur-uji')->firstOrFail();
        $this->post('/admin/manage/grants', ['school_id' => $school->id, 'activity_id' => $activity->id, 'allow_download' => 1, 'is_active' => 1])->assertRedirect()->assertSessionHasNoErrors();
        $grant = AccessGrant::where('school_id', $school->id)->firstOrFail();
        $this->actingAs($partner)->get('/portal/materi/'.$material->id)->assertNotFound();

        $payload['status'] = 'published';
        $this->actingAs($admin)->put('/admin/manage/materials/'.$material->id, $payload)->assertRedirect()->assertSessionHasNoErrors();
        $this->actingAs($partner)->get('/portal/materi/'.$material->id)->assertOk();
        $this->actingAs($admin)->delete('/admin/manage/grants/'.$grant->id)->assertRedirect();
        $this->actingAs($partner)->get('/portal/materi/'.$material->id)->assertNotFound();
    }
}
