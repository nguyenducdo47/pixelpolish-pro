<?php

namespace Tests\Feature;

use App\Filament\Pages\ManageProfile;
use App\Models\User;
use Database\Seeders\LocaleSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class LocaleTabsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(LocaleSeeder::class);
        app()->setLocale('vi');

        Filament::setCurrentPanel(Filament::getPanel('studio'));
    }

    public function test_saving_one_locale_does_not_clear_the_others(): void
    {
        $user = User::factory()->create([
            'username' => 'user-one',
        ]);
        $profile = $user->portfolio->profile;
        $profile->update([
            'headline' => [
                'vi' => 'Vie',
                'en' => 'Eng',
            ],
            'tagline' => [
                'vi' => 'Tag VI',
                'en' => 'Tag EN',
            ],
        ]);

        $this->actingAs($user);

        Livewire::test(ManageProfile::class)
            ->assertSet('data._locale', 'vi')
            ->assertSet('data._current.headline', 'Vie')
            ->set('data._current.headline', 'Vie moi')
            ->set('data._current.tagline', '')
            ->call('save')
            ->assertHasNoErrors();

        $profile->refresh();

        $this->assertSame('Vie moi', $profile->headline['vi'] ?? null);
        $this->assertSame('Eng', $profile->headline['en'] ?? null);
        $this->assertSame('', $profile->tagline['vi'] ?? null);
        $this->assertSame('Tag EN', $profile->tagline['en'] ?? null);
    }

    public function test_switching_locale_loads_that_locale_and_preserves_the_previous(): void
    {
        $user = User::factory()->create([
            'username' => 'user-one',
        ]);
        $user->portfolio->profile->update([
            'headline' => [
                'vi' => 'Vie',
                'en' => 'Eng',
            ],
        ]);

        $this->actingAs($user);

        Livewire::test(ManageProfile::class)
            ->set('data._locale', 'en')
            ->assertSet('data._current.headline', 'Eng')
            ->set('data._current.headline', 'Hello')
            ->call('save')
            ->assertHasNoErrors();

        $profile = $user->portfolio->profile->fresh();

        $this->assertSame('Vie', $profile->headline['vi'] ?? null);
        $this->assertSame('Hello', $profile->headline['en'] ?? null);
    }
}
