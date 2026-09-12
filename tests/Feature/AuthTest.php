<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_can_view_login_and_register_pages(): void
    {
        $this->get('/login')->assertOk();
        $this->get('/register')->assertOk();
        $this->get('/forgot-password')->assertOk();
    }

    public function test_users_can_register_and_are_sent_to_studio(): void
    {
        $response = $this->post('/register', [
            'name' => 'Nguyen Van A',
            'username' => 'nguyenvana',
            'email' => 'vana@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect('/studio/setup');
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'vana@example.com',
            'username' => 'nguyenvana',
            'is_admin' => 0,
        ]);
        $this->assertDatabaseHas('portfolios', [
            'slug' => 'nguyenvana',
        ]);
    }

    public function test_users_are_sent_to_studio_after_login(): void
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ])->assertRedirect('/studio/setup');
    }

    public function test_admins_are_sent_to_admin_after_login(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);

        $this->post('/login', [
            'email' => $admin->email,
            'password' => 'password',
        ])->assertRedirect('/admin');
    }

    public function test_guests_are_redirected_from_studio_to_login(): void
    {
        $this->get('/studio')->assertRedirect('/login');
    }

    public function test_guests_are_redirected_from_admin_to_login(): void
    {
        $this->get('/admin')->assertRedirect('/login');
    }

    public function test_regular_users_cannot_stay_on_the_admin_panel(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/admin')
            ->assertRedirect('/studio/setup');
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('/logout')
            ->assertRedirect('/');

        $this->assertGuest();
    }
}
