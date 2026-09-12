<?php

namespace Tests\Feature;

use App\Filament\Pages\SetupWizard;
use App\Models\User;
use App\Services\PortfolioPresenter;
use App\Services\PortfolioWizardSync;
use App\Support\WizardPendingAvatar;
use Database\Seeders\LocaleSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\FileUploadConfiguration;
use Livewire\Features\SupportFileUploads\FileUploadController;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\Livewire;
use Tests\TestCase;

class SetupWizardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(LocaleSeeder::class);
        Filament::setCurrentPanel(Filament::getPanel('studio'));
    }

    public function test_users_can_open_the_setup_wizard(): void
    {
        $user = User::factory()->create([
            'username' => 'wizard-user',
        ]);

        $this->actingAs($user)
            ->get('/studio/setup')
            ->assertOk()
            ->assertSeeText('Tạo Portfotilo & CV')
            ->assertSee('setup-wizard', false)
            ->assertSeeText('Xem trang như khách truy cập')
            ->assertSeeText('Xem CV bản in')
            ->assertDontSee('h-[70vh]', false);
    }

    public function test_wizard_saves_profile_and_a_project(): void
    {
        $user = User::factory()->create([
            'username' => 'wizard-save',
            'name' => 'Old Name',
        ]);

        $this->actingAs($user);

        Livewire::test(SetupWizard::class)
            ->set('data.username', 'wizard-save')
            ->set('data.full_name', 'New Name')
            ->set('data.email', 'new@example.com')
            ->set('data.headline', ['vi' => 'Dev Laravel'])
            ->set('data._locale', 'vi')
            ->set('data.projects', [[
                'title' => ['vi' => 'Shop'],
                'period' => '2024',
                'is_featured' => true,
            ]])
            ->call('save')
            ->assertHasNoErrors()
            ->assertNotified();

        $user->refresh();

        $this->assertSame('New Name', $user->name);
        $this->assertSame('New Name', $user->portfolio->profile->full_name);
        $this->assertSame('Dev Laravel', $user->portfolio->profile->localeText('headline', 'vi'));
        $this->assertSame('Shop', $user->portfolio->projects()->first()?->localeText('title', 'vi'));
    }

    public function test_unpublished_portfolio_is_hidden_from_guests_but_visible_to_owner(): void
    {
        $user = User::factory()->create([
            'username' => 'preview-owner',
        ]);
        $user->portfolio->update(['is_published' => false]);

        $this->get('/vi/preview-owner')->assertNotFound();

        $this->actingAs($user)
            ->get('/vi/preview-owner')
            ->assertOk();
    }

    public function test_preview_survives_rich_editor_arrays_in_locale_bags(): void
    {
        $user = User::factory()->create([
            'username' => 'wizard-about',
        ]);

        $user->portfolio->profile->update([
            'about' => [
                'vi' => [
                    'type' => 'doc',
                    'content' => [
                        [
                            'type' => 'paragraph',
                            'content' => [
                                ['type' => 'text', 'text' => 'Xin chao'],
                            ],
                        ],
                    ],
                ],
            ],
            'tagline' => [
                'vi' => ['vi' => 'Backend Laravel'],
            ],
        ]);

        $this->actingAs($user)
            ->get('/vi/wizard-about')
            ->assertOk();

        $this->assertStringContainsString('Xin chao', $user->portfolio->profile->fresh()->localeText('about', 'vi'));
        $this->assertSame('Backend Laravel', $user->portfolio->profile->fresh()->localeText('tagline', 'vi'));
    }

    public function test_wizard_converts_rich_editor_state_and_keeps_highlight_lists(): void
    {
        $user = User::factory()->create([
            'username' => 'wizard-rich',
            'name' => 'Rich User',
        ]);

        app(PortfolioWizardSync::class)->saveAll($user->portfolio, [
            'username' => 'wizard-rich',
            'full_name' => 'Rich User',
            '_locale' => 'vi',
            '_current' => [
                'about' => [
                    'type' => 'doc',
                    'content' => [
                        [
                            'type' => 'paragraph',
                            'content' => [
                                ['type' => 'text', 'text' => 'Mo ta'],
                            ],
                        ],
                    ],
                ],
            ],
            'about' => [],
            'projects' => [[
                'title' => ['vi' => 'Shop'],
                'highlights' => ['vi' => ['Nhanh', 'Gon']],
                '_locale' => 'vi',
                '_current' => [
                    'highlights' => ['Nhanh', 'Gon'],
                ],
            ]],
        ]);

        $profile = $user->portfolio->profile->fresh();
        $project = $user->portfolio->projects()->first();

        $this->assertIsString($profile->about['vi'] ?? null);
        $this->assertStringContainsString('Mo ta', $profile->localeText('about', 'vi'));
        $this->assertSame(['Nhanh', 'Gon'], $project?->localeList('highlights', 'vi'));
    }

    public function test_wizard_sync_loads_existing_records(): void
    {
        $user = User::factory()->create([
            'username' => 'wizard-load',
        ]);
        $user->portfolio->projects()->create([
            'title' => ['vi' => 'API'],
            'sort_order' => 0,
        ]);

        $state = app(PortfolioWizardSync::class)->formState($user->portfolio->fresh());

        $this->assertSame('wizard-load', $state['username']);
        $this->assertSame('API', $state['projects'][0]['title']['vi']);
    }

    public function test_wizard_keeps_avatar_in_livewire_tmp_until_finish(): void
    {
        $user = User::factory()->create([
            'username' => 'wizard-avatar',
            'name' => 'Avatar User',
        ]);
        $temp = $this->fakeTempAvatar();

        $this->actingAs($user);

        $this->wizardWithAvatar($user, $temp)->persistStep(app(PortfolioWizardSync::class));

        $this->assertNull($user->portfolio->profile->fresh()->avatar_path);
        $this->assertSame($temp->getFilename(), WizardPendingAvatar::current($user->portfolio->id));
        $this->assertSame([], Storage::disk('public')->files('avatars'));
        Storage::disk(FileUploadConfiguration::disk())->assertExists(
            FileUploadConfiguration::directory().'/'.$temp->getFilename()
        );

        $other = User::factory()->create([
            'username' => 'wizard-avatar-other',
        ]);
        $otherTemp = $this->storeTempAvatar();
        WizardPendingAvatar::remember($other->portfolio->id, $otherTemp);

        $this->wizardWithAvatar($user, $temp)->save(app(PortfolioWizardSync::class));

        $profile = $user->portfolio->profile->fresh();
        $path = $profile->avatar_path;

        $this->assertIsString($path);
        $this->assertStringStartsWith('avatars/', $path);
        Storage::disk('public')->assertExists($path);
        $this->assertNull(WizardPendingAvatar::current($user->portfolio->id));
        Storage::disk(FileUploadConfiguration::disk())->assertMissing(
            FileUploadConfiguration::directory().'/'.$temp->getFilename()
        );
        Storage::disk(FileUploadConfiguration::disk())->assertExists(
            FileUploadConfiguration::directory().'/'.$otherTemp->getFilename()
        );

        $payload = app(PortfolioPresenter::class)->publicPayload($user->portfolio->fresh(), 'vi');

        $this->assertSame($profile->avatarUrl(), $payload['profile']['avatar']);
        $this->assertStringNotContainsString('preview-avatar', (string) $payload['profile']['avatar']);
    }

    public function test_wizard_persist_does_not_clear_an_existing_avatar(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('avatars/old.jpg', 'avatar');

        $user = User::factory()->create([
            'username' => 'wizard-keep-avatar',
        ]);
        $user->portfolio->profile->update(['avatar_path' => 'avatars/old.jpg']);

        $this->actingAs($user);

        Livewire::test(SetupWizard::class)
            ->set('data.username', 'wizard-keep-avatar')
            ->set('data.full_name', $user->name)
            ->set('data.headline', ['vi' => 'Dev Laravel'])
            ->call('persistStep')
            ->assertHasNoErrors();

        $this->assertSame('avatars/old.jpg', $user->portfolio->profile->fresh()->avatar_path);
        Storage::disk('public')->assertExists('avatars/old.jpg');
    }

    public function test_owner_preview_uses_the_pending_livewire_avatar(): void
    {
        $user = User::factory()->create([
            'username' => 'pending-avatar',
        ]);
        $temp = $this->fakeTempAvatar();

        $this->actingAs($user);
        WizardPendingAvatar::remember($user->portfolio->id, $temp);

        $payload = app(PortfolioPresenter::class)->publicPayload($user->portfolio->fresh(), 'vi');

        $this->assertSame(
            route('studio.preview-avatar', ['v' => $temp->getFilename()], false),
            $payload['profile']['avatar']
        );
        $this->assertTrue($payload['cv']['settings']['show_avatar']);

        $this->get(route('studio.preview-avatar'))->assertOk();
    }

    public function test_guests_cannot_fetch_the_pending_wizard_avatar(): void
    {
        $this->get(route('studio.preview-avatar'))->assertRedirect('/login');
    }

    private function wizardWithAvatar(User $user, TemporaryUploadedFile $temp): SetupWizard
    {
        $wizard = Livewire::test(SetupWizard::class)->instance();
        $wizard->data['username'] = $user->username;
        $wizard->data['full_name'] = $user->name;
        $wizard->data['headline'] = ['vi' => 'Dev Laravel'];
        $wizard->data['avatar_path'] = $temp;

        return $wizard;
    }

    private function fakeTempAvatar(): TemporaryUploadedFile
    {
        Storage::fake(FileUploadConfiguration::disk());
        Storage::fake('public');

        return $this->storeTempAvatar();
    }

    private function storeTempAvatar(): TemporaryUploadedFile
    {
        $paths = app(FileUploadController::class)->validateAndStore([
            UploadedFile::fake()->image('avatar.jpg', 80, 80),
        ], FileUploadConfiguration::disk());

        $filename = TemporaryUploadedFile::extractPathFromSignedPath($paths->first());
        $this->assertNotFalse($filename);

        $temp = TemporaryUploadedFile::createFromLivewire($filename);
        $this->assertTrue($temp->exists());

        return $temp;
    }
}
