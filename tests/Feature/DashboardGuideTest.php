<?php

namespace Tests\Feature;

use App\Filament\Widgets\GettingStartedWidget;
use App\Models\User;
use App\Support\PortfolioGuide;
use Database\Seeders\LocaleSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DashboardGuideTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(LocaleSeeder::class);
        app()->setLocale('vi');
    }

    public function test_studio_dashboard_shows_the_portfolio_guide(): void
    {
        $user = User::factory()->create([
            'username' => 'guide-studio',
        ]);

        $this->actingAs($user)
            ->get('/studio?locale=vi')
            ->assertOk()
            ->assertSeeText('Xin chào')
            ->assertSeeText('Quy trình tạo portfolio và CV')
            ->assertSeeText('0/9 bước đã hoàn thành')
            ->assertSeeText('mục chưa hoàn thành')
            ->assertSeeText('Học vấn')
            ->assertSeeText('Ngoại ngữ')
            ->assertSeeText('Chức danh')
            ->assertSeeText('Mở bước này')
            ->assertSeeText('1/4 mục')
            ->assertDontSeeText('Log chỉ ghi vào storage/logs')
            ->assertDontSee('panel.guide.');
    }

    public function test_admin_dashboard_shows_the_portfolio_guide_below_the_welcome_block(): void
    {
        $admin = User::factory()->create([
            'username' => 'guide-admin',
            'is_admin' => true,
        ]);

        $html = $this->actingAs($admin)
            ->get('/admin?locale=vi')
            ->assertOk()
            ->assertSeeText('Xin chào')
            ->assertSeeText('Quy trình tạo portfolio và CV')
            ->assertSeeText('Giao diện')
            ->assertSeeText('0/9 bước đã hoàn thành')
            ->assertDontSeeText('Log chỉ ghi vào storage/logs')
            ->getContent();

        $this->assertLessThan(
            strpos($html, 'Quy trình tạo portfolio và CV'),
            strpos($html, 'Xin chào'),
        );
    }

    public function test_clicking_a_step_tag_shows_its_items(): void
    {
        $user = User::factory()->create([
            'username' => 'guide-click',
        ]);

        Filament::setCurrentPanel(Filament::getPanel('studio'));

        Livewire::actingAs($user)
            ->test(GettingStartedWidget::class)
            ->call('selectStep', 'skills')
            ->assertSet('activeStep', 'skills')
            ->assertSeeText('Danh mục kỹ năng')
            ->assertSeeText('Ít nhất một kỹ năng');
    }

    public function test_new_portfolios_start_with_only_the_name_item_done(): void
    {
        $user = User::factory()->create([
            'username' => 'guide-empty',
        ]);

        Filament::setCurrentPanel(Filament::getPanel('studio'));

        $guide = PortfolioGuide::for($user->portfolio);

        $this->assertSame(0, $guide['steps_done']);
        $this->assertSame(9, $guide['steps_total']);
        $this->assertSame(1, $guide['items_done']);
        $this->assertSame(13, $guide['items_pending']);
        $this->assertSame('profile', $guide['first_incomplete']);
        $this->assertTrue(collect($guide['steps'][0]['items'])->firstWhere('key', 'full_name')['done']);
        $this->assertFalse(collect($guide['steps'][0]['items'])->firstWhere('key', 'headline')['done']);
    }
}
