<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\PortfolioPresenter;
use Database\Seeders\LocaleSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ManageCvTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(LocaleSeeder::class);
        Filament::setCurrentPanel(Filament::getPanel('studio'));
    }

    public function test_locale_preview_and_pdf_actions_are_grouped_in_dropdowns(): void
    {
        $user = User::factory()->create([
            'username' => 'cv-owner',
        ]);

        $this->actingAs($user)
            ->get('/studio/manage-cv?locale=vi')
            ->assertOk()
            ->assertSeeText('Xem CV')
            ->assertSeeText('Tải PDF')
            ->assertSeeText('Tiếng Việt (VI)')
            ->assertSeeText('English (EN)')
            ->assertDontSeeText('Xem vi')
            ->assertDontSeeText('PDF vi');
    }

    public function test_cv_hides_avatar_when_file_is_missing(): void
    {
        Storage::fake('public');

        $user = User::factory()->create([
            'username' => 'no-avatar',
        ]);
        $user->portfolio->update(['is_published' => true]);

        $this->actingAs($user)
            ->get('/studio/manage-cv?locale=vi')
            ->assertOk()
            ->assertDontSeeText('Hiển thị ảnh đại diện');

        $payload = app(PortfolioPresenter::class)->publicPayload($user->portfolio->fresh(), 'vi');

        $this->assertNull($payload['profile']['avatar']);
        $this->assertFalse($payload['cv']['settings']['show_avatar']);
    }

    public function test_cv_shows_avatar_when_file_exists(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('avatars/me.jpg', 'img');

        $user = User::factory()->create([
            'username' => 'has-avatar',
        ]);
        $user->portfolio->profile->update(['avatar_path' => 'avatars/me.jpg']);
        $user->portfolio->cvSettings->update(['show_avatar' => true]);
        $user->portfolio->update(['is_published' => true]);

        $this->actingAs($user)
            ->get('/studio/manage-cv?locale=vi')
            ->assertOk()
            ->assertSeeText('Hiển thị ảnh đại diện');

        $payload = app(PortfolioPresenter::class)->publicPayload($user->portfolio->fresh(), 'vi');

        $this->assertSame('/storage/avatars/me.jpg', $payload['profile']['avatar']);
        $this->assertTrue($payload['cv']['settings']['show_avatar']);
    }

    public function test_empty_avatar_state_is_stored_as_null(): void
    {
        $user = User::factory()->create([
            'username' => 'empty-avatar',
        ]);

        $user->portfolio->profile->update(['avatar_path' => '[]']);

        $this->assertNull($user->portfolio->profile->fresh()->avatar_path);
        $this->assertNull($user->portfolio->profile->fresh()->resolvedAvatarPath());
    }
}
