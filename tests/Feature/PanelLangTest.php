<?php

namespace Tests\Feature;

use App\Filament\Pages\Dashboard;
use App\Filament\Pages\ManageAppearance;
use App\Filament\Pages\ManageCv;
use App\Filament\Pages\ManageMail;
use App\Filament\Pages\ManageProfile;
use App\Filament\Pages\SetupWizard;
use App\Filament\Resources\Projects\ProjectResource;
use App\Filament\Resources\SkillCategories\SkillCategoryResource;
use App\Filament\Resources\Themes\ThemeResource;
use App\Filament\Resources\Users\UserResource;
use App\Models\User;
use Database\Seeders\LocaleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PanelLangTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(LocaleSeeder::class);
    }

    public function test_sidebar_labels_come_from_lang_files(): void
    {
        app()->setLocale('vi');

        $this->assertSame('Tổng quan', Dashboard::getNavigationLabel());
        $this->assertSame('Tạo Portfotilo', SetupWizard::getNavigationLabel());
        $this->assertSame('Hồ sơ', ManageProfile::getNavigationLabel());
        $this->assertSame('Tạo CV', ManageCv::getNavigationLabel());
        $this->assertSame('Giao diện', ManageAppearance::getNavigationLabel());
        $this->assertSame('Theme', ThemeResource::getNavigationLabel());
        $this->assertSame('Cài đặt', ThemeResource::getNavigationGroup());
        $this->assertSame('Dự án', ProjectResource::getNavigationLabel());
        $this->assertSame('Kỹ năng', SkillCategoryResource::getNavigationLabel());
        $this->assertSame('Tài khoản & portfolio', UserResource::getNavigationLabel());
        $this->assertSame('Cài đặt', UserResource::getNavigationGroup());
        $this->assertSame('Cài đặt', ManageMail::getNavigationGroup());
        $this->assertSame('Cấu hình mail', ManageMail::getNavigationLabel());

        app()->setLocale('en');

        $this->assertSame('Overview', Dashboard::getNavigationLabel());
        $this->assertSame('Create Portfotilo', SetupWizard::getNavigationLabel());
        $this->assertSame('Profile', ManageProfile::getNavigationLabel());
        $this->assertSame('Create CV', ManageCv::getNavigationLabel());
        $this->assertSame('Theme', ManageAppearance::getNavigationLabel());
        $this->assertSame('Themes', ThemeResource::getNavigationLabel());
        $this->assertSame('Settings', ThemeResource::getNavigationGroup());
        $this->assertSame('Projects', ProjectResource::getNavigationLabel());
        $this->assertSame('Skills', SkillCategoryResource::getNavigationLabel());
        $this->assertSame('Accounts & portfolios', UserResource::getNavigationLabel());
        $this->assertSame('Settings', UserResource::getNavigationGroup());
        $this->assertSame('Settings', ManageMail::getNavigationGroup());
        $this->assertSame('Mail settings', ManageMail::getNavigationLabel());
    }

    public function test_studio_sidebar_renders_lang_strings_for_the_active_locale(): void
    {
        $user = User::factory()->create([
            'username' => 'lang-user',
        ]);

        $this->actingAs($user)
            ->get('/studio/manage-profile?locale=vi')
            ->assertOk()
            ->assertSeeText('Tổng quan')
            ->assertSeeText('Tạo Portfotilo')
            ->assertSeeText('Hồ sơ')
            ->assertSeeText('Dự án')
            ->assertSeeText('Kỹ năng')
            ->assertSeeText('Tạo CV')
            ->assertSeeText('Giao diện')
            ->assertDontSee('panel.nav.');

        $this->actingAs($user)
            ->get('/studio/manage-profile?locale=en')
            ->assertOk()
            ->assertSeeText('Overview')
            ->assertSeeText('Create Portfotilo')
            ->assertSeeText('Projects')
            ->assertSeeText('Skills')
            ->assertSeeText('Create CV')
            ->assertSeeText('Theme')
            ->assertSeeText('Log out')
            ->assertDontSeeText('Tổng quan')
            ->assertDontSee('panel.nav.');
    }

    public function test_admin_sidebar_renders_lang_strings_for_the_active_locale(): void
    {
        $admin = User::factory()->create([
            'username' => 'lang-admin',
            'is_admin' => true,
        ]);

        $this->actingAs($admin)
            ->get('/admin?locale=vi')
            ->assertOk()
            ->assertSeeText('Tổng quan')
            ->assertSeeText('Tài khoản & portfolio')
            ->assertSeeText('Ngôn ngữ hệ thống')
            ->assertSeeText('Cài đặt')
            ->assertSeeText('Cấu hình mail')
            ->assertSeeText('API dịch')
            ->assertSeeText('Giao diện')
            ->assertSeeText('Theme');

        $this->actingAs($admin)
            ->get('/admin?locale=en')
            ->assertOk()
            ->assertSeeText('Overview')
            ->assertSeeText('Accounts & portfolios')
            ->assertSeeText('System languages')
            ->assertSeeText('Settings')
            ->assertSeeText('Mail settings')
            ->assertSeeText('Translation APIs')
            ->assertSeeText('Theme')
            ->assertSeeText('Themes')
            ->assertDontSeeText('Tổng quan');
    }
}
