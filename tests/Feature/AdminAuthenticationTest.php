<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class AdminAuthenticationTest extends TestCase
{
    public function test_admin_sign_in_shows_email_password_and_remember_me(): void
    {
        $admin = User::factory()->admin()->create();

        $this->get('/admin/login')->assertOk()->assertSee('alt="Ideal Woman"', false)
            ->assertSee('name="password"', false)->assertSee('name="email"', false)->assertSee('Remember me')->assertSee('Forgot password?')->assertDontSee('Team sign in');
        $this->post('/admin/login', ['email' => $admin->email, 'password' => 'password'])->assertRedirect('/admin');

        $this->assertAuthenticatedAs($admin);
    }

    public function test_editors_use_the_same_admin_sign_in_form(): void
    {
        User::factory()->admin()->create(['password' => 'a-different-admin-password']);
        $editor = User::factory()->editor()->create();

        $this->get('/admin/login')->assertOk()->assertSee('name="email"', false)->assertDontSee('Team sign in');
        $this->post('/admin/login', ['email' => $editor->email, 'password' => 'password'])->assertRedirect('/admin');

        $this->assertAuthenticatedAs($editor);
    }

    public function test_email_is_required_even_when_only_one_administrator_exists(): void
    {
        User::factory()->admin()->create();

        $this->post('/admin/login', ['password' => 'password'])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_remember_me_creates_a_persistent_login_cookie(): void
    {
        $admin = User::factory()->admin()->create();

        $this->post('/admin/login', ['email' => $admin->email, 'password' => 'password', 'remember' => '1'])
            ->assertRedirect('/admin')->assertCookie(auth()->guard()->getRecallerName());

        $this->assertAuthenticatedAs($admin);
    }

    public function test_unchecked_remember_me_does_not_create_a_persistent_cookie(): void
    {
        $admin = User::factory()->admin()->create();

        $this->post('/admin/login', ['email' => $admin->email, 'password' => 'password'])
            ->assertRedirect('/admin')->assertCookieMissing(auth()->guard()->getRecallerName());

        $this->assertAuthenticatedAs($admin);
    }

    public function test_login_attempts_are_rate_limited_even_when_email_changes(): void
    {
        User::factory()->admin()->create();
        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->post('/admin/login', ['email' => 'wrong'.$attempt.'@example.org', 'password' => 'incorrect'])->assertSessionHasErrors('email');
        }

        $this->post('/admin/login', ['password' => 'password'])->assertTooManyRequests();

        $this->assertGuest();
    }

    public function test_password_reset_notification_uses_the_trusted_application_url(): void
    {
        Notification::fake();
        config(['app.url' => 'https://idealwoman.example']);
        $admin = User::factory()->admin()->create();

        $this->from('/admin/forgot-password')->post('/admin/forgot-password', ['email' => $admin->email])->assertRedirect('/admin/forgot-password')->assertSessionHas('status');

        Notification::assertSentTo($admin, ResetPassword::class, function (ResetPassword $notification) use ($admin): bool {
            $this->assertStringStartsWith('https://idealwoman.example/admin/reset-password/', $notification->toMail($admin)->actionUrl);
            $this->assertStringContainsString('email='.urlencode($admin->email), $notification->toMail($admin)->actionUrl);

            return true;
        });
        $this->assertDatabaseHas('password_reset_tokens', ['email' => $admin->email]);
    }

    public function test_unknown_or_unprivileged_accounts_do_not_receive_reset_notifications(): void
    {
        Notification::fake();
        $ordinary = User::factory()->create();

        $this->post('/admin/forgot-password', ['email' => 'unknown@example.org'])->assertSessionHas('status');
        $unknownStatus = session('status');
        $this->post('/admin/forgot-password', ['email' => $ordinary->email])->assertSessionHas('status', $unknownStatus);

        Notification::assertNothingSent();
        $this->assertDatabaseCount('password_reset_tokens', 0);
    }

    public function test_reset_requests_are_rate_limited(): void
    {
        for ($attempt = 0; $attempt < 3; $attempt++) {
            $this->post('/admin/forgot-password', ['email' => 'missing'.$attempt.'@example.org'])->assertRedirect();
        }

        $this->post('/admin/forgot-password', ['email' => 'missing@example.org'])->assertTooManyRequests();
    }

    public function test_valid_reset_changes_the_password_rotates_remember_token_and_cannot_be_reused(): void
    {
        $admin = User::factory()->admin()->create(['remember_token' => 'old-remember-token']);
        $token = Password::createToken($admin);
        $payload = ['email' => $admin->email, 'token' => $token, 'password' => 'a-new-secure-password', 'password_confirmation' => 'a-new-secure-password'];
        $this->get('/admin/reset-password/'.$token.'?email='.urlencode($admin->email))->assertOk()->assertSee('Confirm new password');

        $this->post('/admin/reset-password', $payload)->assertRedirect('/admin/login')->assertSessionHas('status');

        $this->assertTrue(Hash::check('a-new-secure-password', $admin->fresh()->password));
        $this->assertFalse(Hash::check('password', $admin->fresh()->password));
        $this->assertNotSame('old-remember-token', $admin->fresh()->remember_token);
        $this->assertGuest();
        $this->assertDatabaseMissing('password_reset_tokens', ['email' => $admin->email]);
        $this->post('/admin/reset-password', $payload)->assertSessionHasErrors('email');
        $this->post('/admin/login', ['email' => $admin->email, 'password' => 'a-new-secure-password'])->assertRedirect('/admin');
        $this->assertAuthenticatedAs($admin);
    }

    public function test_expired_tokens_and_mismatched_confirmation_do_not_change_the_password(): void
    {
        $this->freezeTime();
        $admin = User::factory()->admin()->create();
        $token = Password::createToken($admin);
        $payload = ['email' => $admin->email, 'token' => $token, 'password' => 'a-new-secure-password', 'password_confirmation' => 'different-password'];

        $this->post('/admin/reset-password', $payload)->assertSessionHasErrors('password');
        $payload['password_confirmation'] = $payload['password'];
        $this->travel(61)->minutes();
        $this->post('/admin/reset-password', $payload)->assertSessionHasErrors('email');

        $this->assertTrue(Hash::check('password', $admin->fresh()->password));
    }

    public function test_ordinary_accounts_cannot_reset_through_the_admin_broker_flow(): void
    {
        $user = User::factory()->create();
        $token = Password::createToken($user);

        $this->post('/admin/reset-password', ['email' => $user->email, 'token' => $token, 'password' => 'a-new-secure-password', 'password_confirmation' => 'a-new-secure-password'])->assertSessionHasErrors('email');

        $this->assertTrue(Hash::check('password', $user->fresh()->password));
        $this->assertGuest();
    }

    public static function authenticatedSessionPages(): array
    {
        return ['admin dashboard' => ['/admin'], 'private draft preview' => ['/?preview=home']];
    }

    #[DataProvider('authenticatedSessionPages')]
    public function test_an_admin_session_with_an_old_password_hash_is_rejected_after_reset(string $path): void
    {
        $admin = User::factory()->admin()->create();
        $oldHash = $admin->password;
        $admin->password = 'a-new-secure-password';
        $admin->save();

        $this->actingAs($admin)->withSession(['password_hash_web' => $oldHash])->get($path)->assertRedirect('/admin/login');

        $this->assertGuest();
    }

    public function test_logged_in_admins_return_to_the_dashboard_from_recovery_pages(): void
    {
        $this->actingAs(User::factory()->admin()->create())->get('/admin/forgot-password')->assertRedirect('/admin');
    }
}
