<?php

namespace Tests\Feature;

use App\Models\User;
use App\Support\UiLocale;
use Database\Seeders\LocaleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class UiLocaleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(LocaleSeeder::class);
    }

    public function test_query_locale_stays_on_the_current_page(): void
    {
        $this->get('/login?locale=en')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Auth/Login')
                ->where('locale', 'en')
                ->where('ui.auth.login_title', 'Log in')
            );

        $this->assertSame('en', session(UiLocale::SESSION_KEY));

        $this->get('/login')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('locale', 'en')
            );
    }

    public function test_session_is_used_when_query_is_missing(): void
    {
        $this->withSession([UiLocale::SESSION_KEY => 'en'])
            ->get('/register')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Auth/Register')
                ->where('locale', 'en')
                ->where('ui.auth.register_title', 'Create your portfolio')
            );
    }

    public function test_invalid_query_locale_falls_back_to_vietnamese(): void
    {
        $this->get('/login?locale=zz')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('locale', 'vi')
                ->where('ui.auth.login_title', 'Đăng nhập')
            );
    }

    public function test_studio_keeps_the_current_route_when_switching_locale(): void
    {
        $user = User::factory()->create([
            'username' => 'locale-user',
        ]);

        $this->actingAs($user)
            ->get('/studio/setup?locale=en')
            ->assertOk()
            ->assertSee('Profile', false)
            ->assertDontSee('Hồ sơ', false);

        $this->actingAs($user)
            ->get('/studio/setup?locale=vi')
            ->assertOk()
            ->assertSee('Hồ sơ', false);
    }
}
