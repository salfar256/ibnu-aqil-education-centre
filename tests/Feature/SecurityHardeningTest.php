<?php

namespace Tests\Feature;

use App\Models\{Activity, Asset, Material, School, User};
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\{Request, UploadedFile};
use Illuminate\Support\Facades\{Hash, Password, Storage, URL};
use Tests\TestCase;

class SecurityHardeningTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['app.url' => 'http://localhost']);
    }

    private function account(string $email = 'recovery@example.test', string $role = 'partner'): User
    {
        $school = School::create(['name' => 'Sekolah pengujian keamanan', 'code' => uniqid('HARDEN-'), 'is_active' => true]);

        return User::create([
            'school_id' => $school->id,
            'name' => 'Pengguna pengujian',
            'email' => $email,
            'password' => 'Correct!Password2026',
            'role' => $role,
            'is_active' => true,
            'must_change_password' => false,
        ]);
    }

    private function uploadMaterial(): Material
    {
        Storage::fake('private');
        $this->actingAs($this->account('editor-security@example.test', 'editor'));
        $activity = Activity::create(['title' => 'Kegiatan pengujian', 'slug' => 'kegiatan-keamanan', 'theme' => 'Praktik baik', 'year' => 2026, 'status' => 'published']);

        return Material::create(['activity_id' => $activity->id, 'title' => 'Materi pengujian', 'slug' => 'materi-keamanan', 'description' => 'Materi uji', 'type' => 'pdf', 'author_name' => 'Pengelola uji', 'status' => 'published', 'published_at' => now()]);
    }

    private function pdf(string $catalogExtras = '', array $extraObjects = []): string
    {
        $objects = array_merge([
            '<< /Type /Catalog /Pages 2 0 R '.$catalogExtras.' >>',
            '<< /Type /Pages /Kids [3 0 R] /Count 1 >>',
            '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 100 100] >>',
        ], $extraObjects);
        $pdf = "%PDF-1.4\n";
        $offsets = [];
        foreach ($objects as $index => $object) {
            $offsets[] = strlen($pdf);
            $pdf .= ($index + 1)." 0 obj\n".$object."\nendobj\n";
        }
        $xref = strlen($pdf);
        $pdf .= 'xref'."\n0 ".(count($objects) + 1)."\n0000000000 65535 f \n";
        foreach ($offsets as $offset) {
            $pdf .= sprintf("%010d 00000 n \n", $offset);
        }

        return $pdf.'trailer'."\n<< /Size ".(count($objects) + 1)." /Root 1 0 R >>\nstartxref\n".$xref."\n%%EOF";
    }

    public function test_pdf_with_escaped_javascript_names_is_rejected_without_stored_assets(): void
    {
        $material = $this->uploadMaterial();
        $pdf = $this->pdf('/Open#41ction 4 0 R', ['<< /Type /Action /S /Java#53cript /J#53 (app.alert\\(1\\)) >>']);

        $this->post('/admin/assets', ['material_id' => $material->id, 'files' => [UploadedFile::fake()->createWithContent('escaped-active.pdf', $pdf)]])
            ->assertRedirect()
            ->assertSessionHasErrors('file');
        $this->assertDatabaseCount('assets', 0);
        $this->assertSame([], Storage::disk('private')->allFiles());
    }

    public function test_pdf_with_compressed_object_stream_is_rejected_even_when_name_is_escaped(): void
    {
        $material = $this->uploadMaterial();
        $compressed = gzcompress('5 0 << /Type /Example >>');
        $pdf = $this->pdf('', ['<< /Type /Obj#53tm /N 1 /First 4 /Length '.strlen($compressed).' /Filter /FlateDecode >>'."\nstream\n".$compressed."\nendstream"]);

        $this->post('/admin/assets', ['material_id' => $material->id, 'files' => [UploadedFile::fake()->createWithContent('object-stream.pdf', $pdf)]])
            ->assertRedirect()
            ->assertSessionHasErrors('file');
        $this->assertDatabaseCount('assets', 0);
        $this->assertSame([], Storage::disk('private')->allFiles());
    }

    public function test_ordinary_pdf_remains_readable_after_upload_hardening(): void
    {
        $material = $this->uploadMaterial();
        $pdf = $this->pdf();

        $this->post('/admin/assets', ['material_id' => $material->id, 'allow_download' => 1, 'files' => [UploadedFile::fake()->createWithContent('ordinary.pdf', $pdf)]])
            ->assertRedirect()
            ->assertSessionHasNoErrors();
        $asset = Asset::sole();
        $this->assertSame('private', $asset->visibility);
        $this->assertSame($pdf, Storage::disk('private')->get($asset->path));
        $this->get('/berkas/'.$asset->id)->assertOk()->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_controlled_video_cannot_be_published_and_never_embeds_without_provider_integration(): void
    {
        $material = $this->uploadMaterial();
        $payload = ['activity_id' => $material->activity_id, 'title' => 'Video privat uji', 'slug' => 'video-privat-uji', 'description' => 'Materi video uji', 'type' => 'video', 'author_name' => 'Pengelola uji', 'status' => 'published', 'video_provider' => 'youtube', 'video_id' => 'dQw4w9WgXcQ', 'video_protection' => 'controlled'];
        config(['portal.controlled_video_enabled' => false]);

        $this->post('/admin/manage/materials', $payload)
            ->assertRedirect()
            ->assertSessionHasErrors('video_protection');
        $this->assertDatabaseMissing('materials', ['slug' => 'video-privat-uji']);

        $payload['status'] = 'draft';
        $this->post('/admin/manage/materials', $payload)->assertRedirect()->assertSessionHasNoErrors();
        $video = Material::where('slug', 'video-privat-uji')->sole();
        $this->get('/portal/materi/'.$video->id)->assertOk()->assertDontSee('<iframe', false)->assertDontSee($payload['video_id']);
    }

    public function test_malformed_reset_token_is_rejected_before_broker_and_does_not_change_password(): void
    {
        $user = $this->account();
        Password::createToken($user);
        $payload = [
            'email' => $user->email,
            'password' => 'Recovered!Password2027',
            'password_confirmation' => 'Recovered!Password2027',
        ];

        foreach ([['unexpected-array'], str_repeat('x', 256)] as $invalidToken) {
            $this->post('/password/reset', $payload + ['token' => $invalidToken])
                ->assertRedirect()
                ->assertSessionHasErrors('token');
            $this->assertTrue(Hash::check('Correct!Password2026', $user->fresh()->password));
            $this->assertGuest();
        }
    }

    public function test_reset_email_uses_configured_origin_even_when_request_host_is_hostile(): void
    {
        $user = $this->account();
        config(['app.url' => 'https://portal.example.test/application']);
        URL::setRequest(Request::create('https://attacker.example/password/email', 'POST'));

        $message = (new ResetPassword('example-reset-token'))->toMail($user);

        $this->assertSame(
            'https://portal.example.test/application/password/reset/example-reset-token?email='.rawurlencode($user->email),
            $message->actionUrl,
        );
        $this->assertStringNotContainsString('attacker.example', $message->actionUrl);
    }

    public function test_admin_account_email_is_normalized_and_case_variant_duplicate_is_rejected(): void
    {
        $admin = $this->account('admin-security@example.test', 'admin');
        $this->actingAs($admin);
        $payload = [
            'name' => 'Pengguna email campuran',
            'email' => '  Mixed.Case@Example.Test  ',
            'school_id' => $admin->school_id,
            'role' => 'partner',
            'is_active' => 1,
            'send_reset' => 0,
        ];

        $this->post('/admin/manage/users', $payload)
            ->assertRedirect()
            ->assertSessionHasNoErrors();
        $this->assertDatabaseHas('users', ['email' => 'mixed.case@example.test', 'role' => 'partner']);

        $payload['email'] = 'MIXED.CASE@EXAMPLE.TEST';
        $this->post('/admin/manage/users', $payload)
            ->assertRedirect()
            ->assertSessionHasErrors('email');
        $this->assertSame(1, User::where('email', 'mixed.case@example.test')->count());
    }
}

