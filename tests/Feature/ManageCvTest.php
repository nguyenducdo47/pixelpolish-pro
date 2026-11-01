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
        $cv = $user->portfolio->defaultCv;
        $cvPayload = app(PortfolioPresenter::class)->cvPayload($user->portfolio->fresh(), 'vi', $cv);
        $this->assertFalse($cvPayload['cv']['settings']['show_avatar']);
    }

    public function test_cv_shows_avatar_when_file_exists(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('avatars/me.jpg', 'img');

        $user = User::factory()->create([
            'username' => 'has-avatar',
        ]);
        $user->portfolio->profile->update(['avatar_path' => 'avatars/me.jpg']);
        $cv = $user->portfolio->defaultCv;
        $cv->update(['show_avatar' => true]);
        $user->portfolio->update(['is_published' => true]);

        $this->actingAs($user)
            ->get('/studio/manage-cv?locale=vi')
            ->assertOk()
            ->assertSeeText('Hiển thị ảnh đại diện');

        $payload = app(PortfolioPresenter::class)->cvPayload($user->portfolio->fresh(), 'vi', $cv);

        $this->assertSame('/storage/avatars/me.jpg', $payload['profile']['avatar']);
        $this->assertTrue($payload['cv']['settings']['show_avatar']);
    }

    public function test_cv_includes_work_experience_when_configured(): void
    {
        $user = User::factory()->create(['username' => 'cv-work']);

        $cv = $user->portfolio->defaultCv;

        $user->portfolio->workExperiences()->create([
            'cv_id' => $cv->id,
            'company' => ['vi' => 'Công ty ABC', 'en' => 'ABC Co.'],
            'position' => ['vi' => 'Lập trình viên', 'en' => 'Developer'],
            'details' => ['vi' => '', 'en' => ''],
            'period' => '2024 – 2026',
            'sort_order' => 0,
        ]);

        $payload = app(PortfolioPresenter::class)->cvPayload($user->portfolio->fresh(), 'vi', $cv);

        $this->assertCount(1, $payload['work_experiences']);
        $this->assertSame('Công ty ABC', $payload['work_experiences'][0]['company']);
    }

    public function test_default_cv_includes_work_experience_when_cv_id_is_null(): void
    {
        $user = User::factory()->create(['username' => 'cv-work-null']);

        $cv = $user->portfolio->defaultCv;

        $user->portfolio->workExperiences()->create([
            'cv_id' => null,
            'company' => ['vi' => 'Công ty XYZ', 'en' => 'XYZ Co.'],
            'position' => ['vi' => 'Dev', 'en' => 'Dev'],
            'details' => ['vi' => '', 'en' => ''],
            'period' => '2024 – 2026',
            'sort_order' => 0,
        ]);

        $payload = app(PortfolioPresenter::class)->cvPayload($user->portfolio->fresh(), 'vi', $cv);

        $this->assertCount(1, $payload['work_experiences']);
        $this->assertSame('Công ty XYZ', $payload['work_experiences'][0]['company']);
    }

    public function test_cv_includes_resume_summary_when_configured(): void
    {
        $user = User::factory()->create(['username' => 'cv-summary']);

        $cv = $user->portfolio->defaultCv;
        $cv->update([
            'resume_summary_title' => ['vi' => 'Tiêu đề CV', 'en' => 'CV heading'],
            'resume_summary' => ['vi' => 'Tóm tắt thử', 'en' => 'Sample summary'],
            'show_resume_summary' => true,
        ]);

        $payload = app(PortfolioPresenter::class)->cvPayload($user->portfolio->fresh(), 'vi', $cv);

        $this->assertSame('Tiêu đề CV', $payload['cv']['resume_summary_title']);
        $this->assertSame('Tóm tắt thử', $payload['cv']['resume_summary']);
        $this->assertTrue($payload['cv']['settings']['show_resume_summary']);
    }

    public function test_cv_settings_include_project_period_and_social_toggles(): void
    {
        $user = User::factory()->create(['username' => 'cv-toggles']);

        $cv = $user->portfolio->defaultCv;
        $cv->update([
            'show_project_period' => false,
            'show_social_links' => false,
        ]);

        $payload = app(PortfolioPresenter::class)->cvPayload($user->portfolio->fresh(), 'vi', $cv);

        $this->assertFalse($payload['cv']['settings']['show_project_period']);
        $this->assertFalse($payload['cv']['settings']['show_social_links']);
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
