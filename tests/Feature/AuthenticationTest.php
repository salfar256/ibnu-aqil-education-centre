<?php

namespace Tests\Feature;

use App\Models\{School, User};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\{Hash, Notification, Password};
use Illuminate\Auth\Notifications\ResetPassword;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    private function account(array $attributes = [], bool $schoolActive = true): User
    {
        $school = School::create(['name' => 'Sekolah Uji Akun', 'code' => uniqid('AUTH-'), 'is_active' => $schoolActive]);

        return User::create(array_merge(['school_id' => $school->id, 'name' => 'Pengguna Uji', 'email' => uniqid('auth-').'@example.test', 'password' => Hash::make('Correct!Password2026'), 'role' => 'partner', 'is_active' => true, 'must_change_password' => false], $attributes));
    }

    public function test_login_and_logout_protect_portal_session(): void
    {
        $user = $this->account();
        $this->get('/login')->assertOk();
        $this->post('/login', ['email' => $user->email, 'password' => 'Correct!Password2026'])->assertRedirect();
        $this->assertAuthenticatedAs($user);
        $this->get('/portal')->assertOk();
        $this->post('/logout')->assertRedirect();
        $this->assertGuest();
        $this->get('/portal')->assertRedirect('/login');
    }

    public function test_self_registration_is_not_available(): void
    {
        $this->get('/register')->assertNotFound();
        $this->post('/register', ['name' => 'Tidak diundang', 'email' => 'uninvited@example.test', 'password' => 'Correct!Password2026', 'password_confirmation' => 'Correct!Password2026'])->assertNotFound();
        $this->assertDatabaseMissing('users', ['email' => 'uninvited@example.test']);
    }

    public function test_disabled_account_and_disabled_school_cannot_login(): void
    {
        foreach ([$this->account(['is_active' => false]), $this->account([], false)] as $user) {
            $this->post('/login', ['email' => $user->email, 'password' => 'Correct!Password2026'])->assertRedirect()->assertSessionHasErrors('email');
            $this->assertGuest();
        }
    }

    public function test_existing_session_is_rejected_when_school_or_user_is_disabled(): void
    {
        $user = $this->account();
        $this->actingAs($user)->get('/portal')->assertOk();
        $user->update(['is_active' => false]);
        $this->actingAs($user->fresh())->get('/portal')->assertForbidden();
        $this->assertGuest();

        $other = $this->account();
        $other->school->update(['is_active' => false]);
        $this->actingAs($other->fresh())->get('/portal')->assertForbidden();
        $this->assertGuest();
    }

    public function test_repeated_failed_login_blocks_even_the_correct_password_temporarily(): void
    {
        $user = $this->account();
        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->post('/login', ['email' => $user->email, 'password' => 'wrong-password'])->assertRedirect()->assertSessionHasErrors('email');
        }
        $this->post('/login', ['email' => $user->email, 'password' => 'Correct!Password2026'])->assertRedirect()->assertSessionHasErrors('email');
        $this->assertGuest();

        $this->travel(6)->minutes();
        $this->post('/login', ['email' => $user->email, 'password' => 'Correct!Password2026'])->assertRedirect()->assertSessionHasNoErrors();
        $this->assertAuthenticatedAs($user);
    }

    public function test_first_login_must_change_password_before_portal_access(): void
    {
        $user = $this->account(['must_change_password' => true]);
        $this->actingAs($user)->get('/portal')->assertRedirect('/akun/kata-sandi');
        $this->get('/akun/kata-sandi')->assertOk();
        $this->put('/akun/kata-sandi', ['current_password' => 'Correct!Password2026', 'password' => 'Changed!Password2027', 'password_confirmation' => 'Changed!Password2027'])->assertRedirect()->assertSessionHasNoErrors();
        $this->assertFalse($user->fresh()->must_change_password);
        $this->assertTrue(Hash::check('Changed!Password2027', $user->fresh()->password));
        $this->actingAs($user->fresh())->get('/portal')->assertOk();
    }

    public function test_password_change_requires_correct_current_password_and_confirmation(): void
    {
        $user = $this->account();
        $this->actingAs($user)->put('/akun/kata-sandi', ['current_password' => 'incorrect', 'password' => 'Changed!Password2027', 'password_confirmation' => 'Changed!Password2027'])->assertRedirect()->assertSessionHasErrors('current_password');
        $this->assertTrue(Hash::check('Correct!Password2026', $user->fresh()->password));
        $this->put('/akun/kata-sandi', ['current_password' => 'Correct!Password2026', 'password' => 'Changed!Password2027', 'password_confirmation' => 'different'])->assertRedirect()->assertSessionHasErrors('password');
        $this->assertTrue(Hash::check('Correct!Password2026', $user->fresh()->password));
    }

    public function test_recovery_email_sends_framework_reset_notification(): void
    {
        Notification::fake();
        $user = $this->account();

        $this->post('/password/email', ['email' => $user->email])->assertRedirect()->assertSessionHasNoErrors();
        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_password_reset_rejects_invalid_token_and_token_cannot_be_reused(): void
    {
        $user = $this->account(['must_change_password' => true]);
        $payload = ['email' => $user->email, 'password' => 'Recovered!Password2027', 'password_confirmation' => 'Recovered!Password2027'];

        $this->post('/password/reset', $payload + ['token' => 'invalid-token'])->assertRedirect()->assertSessionHasErrors();
        $this->assertTrue(Hash::check('Correct!Password2026', $user->fresh()->password));
        $token = Password::createToken($user);
        $this->post('/password/reset', $payload + ['token' => $token])->assertRedirect()->assertSessionHasNoErrors();
        $this->assertTrue(Hash::check('Recovered!Password2027', $user->fresh()->password));
        $this->assertFalse($user->fresh()->must_change_password);

        $this->post('/password/reset', $payload + ['token' => $token])->assertRedirect()->assertSessionHasErrors();
        $this->assertGuest();
    }
}
