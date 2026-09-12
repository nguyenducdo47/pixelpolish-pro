<?php

namespace Tests\Feature;

use App\Filament\Pages\ManageAppearance;
use App\Filament\Resources\Themes\Pages\ManageThemes;
use App\Models\Theme;
use App\Models\User;
use App\Support\AppearanceTheme;
use Database\Seeders\LocaleSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use InvalidArgumentException;
use Livewire\Livewire;
use Tests\TestCase;

class AppearanceThemeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(LocaleSeeder::class);
        Filament::setCurrentPanel(Filament::getPanel('studio'));
    }

    public function test_default_themes_are_seeded(): void
    {
        $this->assertSame(6, Theme::query()->count());
        $this->assertSame('aurora', Theme::defaultEnabled()?->slug);
    }

    public function test_resolve_uses_the_default_enabled_theme(): void
    {
        $user = User::factory()->create(['username' => 'theme-default']);

        $resolved = AppearanceTheme::resolve($user->portfolio);

        $this->assertSame('aurora', $resolved['preset']);
        $this->assertSame('#1f99a8', $resolved['colors']['primary']);
    }

    public function test_user_can_customize_an_enabled_theme_without_changing_the_catalog(): void
    {
        $user = User::factory()->create(['username' => 'theme-owner']);
        $midnight = Theme::query()->where('slug', 'midnight')->first();

        AppearanceTheme::apply($user->portfolio, [
            'theme_id' => $midnight->id,
            'colors' => ['primary' => '#ff0000'],
        ]);

        $user->portfolio->refresh();
        $midnight->refresh();

        $this->assertSame($midnight->id, $user->portfolio->theme_id);
        $this->assertSame('#ff0000', AppearanceTheme::resolve($user->portfolio)['colors']['primary']);
        $this->assertSame('#4f46e5', $midnight->colors['primary']);
        $this->assertSame('modern', $user->portfolio->cvSettings->template);
    }

    public function test_disabled_theme_cannot_be_applied(): void
    {
        $user = User::factory()->create(['username' => 'theme-disabled']);
        $forest = Theme::query()->where('slug', 'forest')->first();
        $forest->update(['is_enabled' => false]);

        AppearanceTheme::apply($user->portfolio, ['theme_id' => $forest->id]);

        $this->assertNotSame($forest->id, $user->portfolio->fresh()->theme_id);
        $this->assertSame('aurora', AppearanceTheme::assignedTheme($user->portfolio->fresh())?->slug);
    }

    public function test_import_rejects_invalid_json(): void
    {
        $this->expectException(InvalidArgumentException::class);

        AppearanceTheme::import('not-json');
    }

    public function test_studio_can_open_appearance_and_save_an_enabled_theme(): void
    {
        $user = User::factory()->create(['username' => 'theme-studio']);
        $ocean = Theme::query()->where('slug', 'ocean')->first();

        $this->actingAs($user)
            ->get('/studio/manage-appearance?locale=vi')
            ->assertOk()
            ->assertSeeText('Giao diện và bố cục')
            ->assertSeeText('Ocean');

        Livewire::test(ManageAppearance::class)
            ->fillForm([
                'theme_id' => $ocean->id,
                ...$ocean->definition(),
            ])
            ->call('save');

        $this->assertSame($ocean->id, $user->portfolio->fresh()->theme_id);
    }

    public function test_admin_can_manage_catalog_themes(): void
    {
        $admin = User::factory()->create([
            'username' => 'theme-admin',
            'is_admin' => true,
        ]);

        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $this->actingAs($admin)
            ->get('/admin/themes?locale=vi')
            ->assertOk()
            ->assertSeeText('Quản lý theme')
            ->assertSeeText('Aurora');

        Livewire::actingAs($admin)
            ->test(ManageThemes::class)
            ->callAction('create', data: [
                'name' => 'Studio Red',
                'slug' => 'studio-red',
                'is_enabled' => true,
                'layout' => 'centered',
                'hero' => 'minimal',
                'radius' => 'md',
                'font' => 'sans',
                'density' => 'comfortable',
                'cv_layout' => 'modern',
                'show_particles' => false,
                'colors' => AppearanceTheme::preset('aurora')['colors'],
                'dark' => AppearanceTheme::preset('aurora')['dark'],
            ]);

        $this->assertTrue(Theme::query()->where('slug', 'studio-red')->where('is_enabled', true)->exists());
    }

    public function test_admin_can_toggle_a_theme_from_the_catalog_table(): void
    {
        $admin = User::factory()->create([
            'username' => 'theme-toggle',
            'is_admin' => true,
        ]);
        $forest = Theme::query()->where('slug', 'forest')->first();
        $aurora = Theme::query()->where('slug', 'aurora')->first();

        Filament::setCurrentPanel(Filament::getPanel('admin'));

        Livewire::actingAs($admin)
            ->test(ManageThemes::class)
            ->call('updateTableColumnState', 'is_enabled', (string) $forest->id, false)
            ->call('updateTableColumnState', 'is_enabled', (string) $aurora->id, false);

        $this->assertFalse($forest->fresh()->is_enabled);
        $this->assertTrue($aurora->fresh()->is_enabled);
    }

    public function test_studio_only_lists_enabled_themes(): void
    {
        $user = User::factory()->create(['username' => 'theme-hidden']);
        Theme::query()->where('slug', 'forest')->update(['is_enabled' => false]);

        $this->actingAs($user)
            ->get('/studio/manage-appearance?locale=vi')
            ->assertOk()
            ->assertSeeText('Ocean')
            ->assertDontSeeText('Forest');
    }

    public function test_studio_cannot_open_theme_catalog(): void
    {
        $user = User::factory()->create(['username' => 'theme-studio-catalog']);

        $this->actingAs($user)
            ->get('/studio/themes')
            ->assertForbidden();
    }

    public function test_public_portfolio_receives_resolved_theme_tokens(): void
    {
        $user = User::factory()->create(['username' => 'theme-public']);
        $user->portfolio->update(['is_published' => true]);
        $forest = Theme::query()->where('slug', 'forest')->first();

        AppearanceTheme::apply($user->portfolio, ['theme_id' => $forest->id]);

        $this->get('/vi/theme-public')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Portfolio/Show')
                ->where('appearance.preset', 'forest')
                ->where('appearance.cv_layout', 'classic')
                ->where('appearance.css.--pf-primary', '#166534')
                ->where('theme', 'system'));
    }

    public function test_public_portfolio_uses_the_owner_default_theme_mode(): void
    {
        $user = User::factory()->create(['username' => 'theme-mode']);
        $user->portfolio->update([
            'is_published' => true,
            'default_theme' => 'dark',
        ]);

        $this->get('/vi/theme-mode')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Portfolio/Show')
                ->where('theme', 'dark'));
    }
}
