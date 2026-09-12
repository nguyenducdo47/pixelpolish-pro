<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use Database\Seeders\LocaleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(LocaleSeeder::class);
    }

    public function test_guests_can_view_the_forgot_password_page(): void
    {
        $this->get('/forgot-password')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Auth/ForgotPassword')
                ->where('ui.auth.forgot_title', 'Quên mật khẩu')
            );
    }

    public function test_reset_link_is_sent_for_an_existing_email(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->from('/forgot-password')
            ->post('/forgot-password', ['email' => $user->email])
            ->assertRedirect('/forgot-password')
            ->assertSessionHas('status');

        Notification::assertSentTo($user, ResetPasswordNotification::class, function (ResetPasswordNotification $notification) use ($user): bool {
            $mail = $notification->toMail($user);

            return str_contains($mail->actionUrl, 'locale=vi')
                && str_contains($mail->actionUrl, 'reset-password/');
        });
    }

    public function test_unknown_email_does_not_send_a_reset_link(): void
    {
        Notification::fake();

        $this->from('/forgot-password')
            ->post('/forgot-password', ['email' => 'nobody@example.com'])
            ->assertRedirect('/forgot-password')
            ->assertSessionHasErrors('email');

        Notification::assertNothingSent();
    }

    public function test_guests_can_view_the_reset_password_page(): void
    {
        $this->get('/reset-password/test-token?email=user@example.com')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Auth/ResetPassword')
                ->where('token', 'test-token')
                ->where('email', 'user@example.com')
            );
    }

    public function test_users_can_reset_their_password_and_are_signed_in(): void
    {
        $user = User::factory()->create();
        $token = Password::broker()->createToken($user);

        $this->post('/reset-password', [
            'token' => $token,
            'email' => $user->email,
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ])->assertRedirect('/studio/setup');

        $this->assertAuthenticatedAs($user);
        $this->assertTrue(Hash::check('new-password', $user->fresh()->password));
    }

    public function test_invalid_reset_token_is_rejected(): void
    {
        $user = User::factory()->create([
            'password' => 'old-password',
        ]);

        $this->from('/reset-password/invalid')
            ->post('/reset-password', [
                'token' => 'invalid-token',
                'email' => $user->email,
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ])
            ->assertRedirect('/reset-password/invalid')
            ->assertSessionHasErrors('email');

        $this->assertGuest();
        $this->assertTrue(Hash::check('old-password', $user->fresh()->password));
    }
}
