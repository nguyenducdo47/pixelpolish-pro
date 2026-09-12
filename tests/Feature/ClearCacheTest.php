<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\LocaleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClearCacheTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(LocaleSeeder::class);
    }

    public function test_studio_and_admin_topbars_show_the_clear_cache_button(): void
    {
        $user = User::factory()->create([
            'username' => 'cache-studio',
        ]);
        $admin = User::factory()->create([
            'username' => 'cache-admin',
            'is_admin' => true,
        ]);

        $this->actingAs($user)
            ->get('/studio?locale=vi')
            ->assertOk()
            ->assertSeeText('Xóa cache')
            ->assertSee(route('cache.clear'), false);

        $this->actingAs($admin)
            ->get('/admin?locale=vi')
            ->assertOk()
            ->assertSeeText('Xóa cache')
            ->assertSee(route('cache.clear'), false);
    }

    public function test_authenticated_users_can_clear_the_cache(): void
    {
        $user = User::factory()->create([
            'username' => 'cache-clear',
        ]);

        $this->actingAs($user)
            ->from('/studio')
            ->post(route('cache.clear'))
            ->assertRedirect('/studio');
    }

    public function test_guests_cannot_clear_the_cache(): void
    {
        $this->post(route('cache.clear'))->assertRedirect('/login');
    }
}
