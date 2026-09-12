<?php

namespace Tests\Feature;

use App\Filament\Resources\TranslationApis\Pages\ManageTranslationApis;
use App\Models\TranslationApi;
use App\Models\User;
use Database\Seeders\LocaleSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TranslationApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(LocaleSeeder::class);
        Filament::setCurrentPanel(Filament::getPanel('admin'));
    }

    public function test_empty_database_uses_hardcoded_defaults(): void
    {
        $this->assertSame([], TranslationApi::query()->pluck('id')->all());

        $active = TranslationApi::active();

        $this->assertCount(3, $active);
        $this->assertSame('google_chrome', $active->first()->driver);
        $this->assertSame('https://clients5.google.com/translate_a/t', $active->first()->url);
    }

    public function test_admins_can_open_translation_api_settings(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
            'username' => 'translate-admin',
        ]);

        $this->actingAs($admin)
            ->get('/admin/translation-apis?locale=vi')
            ->assertOk()
            ->assertSeeText('API dịch')
            ->assertSeeText('Khôi phục mặc định')
            ->assertSeeText('MyMemory');

        $this->assertSame(3, TranslationApi::query()->count());
    }

    public function test_translation_apis_are_hidden_from_studio(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
            'username' => 'translate-studio',
        ]);

        Filament::setCurrentPanel(Filament::getPanel('studio'));

        $this->actingAs($admin)
            ->get('/studio/translation-apis')
            ->assertForbidden();
    }

    public function test_restore_defaults_adds_missing_drivers_only(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
            'username' => 'translate-restore',
        ]);

        TranslationApi::query()->create([
            'name' => 'MyMemory custom',
            'driver' => 'mymemory',
            'method' => 'POST',
            'url' => 'https://translate.example.test/get',
            'is_enabled' => true,
            'sort_order' => 1,
        ]);

        $this->actingAs($admin);

        Livewire::test(ManageTranslationApis::class)
            ->callAction('restoreDefaults');

        $this->assertSame(3, TranslationApi::query()->count());
        $this->assertSame(
            'https://translate.example.test/get',
            TranslationApi::query()->where('driver', 'mymemory')->value('url'),
        );
    }
}
