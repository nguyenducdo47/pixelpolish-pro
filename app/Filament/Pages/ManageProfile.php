<?php

namespace App\Filament\Pages;

use App\Enums\ContentProfile;
use App\Filament\Concerns\TranslatesPage;
use App\Support\ContentProfileConfig;
use App\Filament\Forms\LocaleTabs;
use App\Models\Portfolio;
use App\Models\Profile;
use App\Models\UserMailPreference;
use App\Support\LocaleCatalog;
use App\Support\UiLocale;
use App\Support\WizardPendingAvatar;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Validation\Rule;

class ManageProfile extends Page
{
    use TranslatesPage;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedUserCircle;

    protected static ?string $navigationLabel = 'panel.nav.profile';

    protected static ?int $navigationSort = 1;

    protected static ?string $title = 'panel.pages.profile';

    protected string $view = 'filament.pages.manage-profile';

    public ?array $data = [];

    public function mount(): void
    {
        $portfolio = $this->portfolio();
        $profile = $portfolio->profile;

        $user = auth()->user();
        $pref = UserMailPreference::forUser($user);

        $this->form->fill([
            '_locale' => UiLocale::current(),
            'username' => $user->username,
            'marketing_opt_in' => $pref->wantsMarketingEmails(),
            'full_name' => $profile->full_name,
            'email' => $profile->email,
            'phone' => $profile->phone,
            'location' => $profile->location,
            'website' => $profile->website,
            'date_of_birth' => $profile->date_of_birth?->toDateString(),
            'avatar_path' => $profile->resolvedAvatarPath(),
            'headline' => $profile->headline,
            'tagline' => $profile->tagline,
            'about' => $profile->about,
            'philosophy_quote' => $profile->philosophy_quote,
            'is_published' => $portfolio->is_published,
            'default_locale' => $portfolio->default_locale,
            'default_theme' => $portfolio->default_theme,
            'content_profile' => $portfolio->content_profile?->value ?? ContentProfile::It->value,
            'seo_title' => $portfolio->seo_title,
            'seo_description' => $portfolio->seo_description,
        ]);
    }

    public function defaultForm(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->model($this->profile());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('panel.sections.publishing'))->schema([
                    TextInput::make('username')
                        ->label(__('panel.fields.username'))
                        ->required()
                        ->alphaDash()
                        ->rules([
                            Rule::unique('users', 'username')->ignore(auth()->id()),
                        ]),
                    Toggle::make('is_published')->label(__('panel.fields.publish')),
                    Select::make('default_locale')
                        ->label(__('panel.fields.default_locale'))
                        ->options(fn () => LocaleCatalog::options())
                        ->native(false)
                        ->required(),
                    Select::make('default_theme')
                        ->label(__('panel.fields.default_theme'))
                        ->options([
                            'system' => __('panel.fields.theme_system'),
                            'light' => __('panel.fields.theme_light'),
                            'dark' => __('panel.fields.theme_dark'),
                        ])
                        ->native(false)
                        ->required(),
                    Select::make('content_profile')
                        ->label(__('panel.fields.content_profile'))
                        ->helperText(function (): string {
                            $slug = ContentProfileConfig::for($this->portfolio())->suggestedThemeSlug();
                            $base = __('panel.fields.content_profile_helper');

                            if (! $slug) {
                                return $base;
                            }

                            return $base.' '.__('panel.fields.content_profile_theme_hint', ['theme' => $slug]);
                        })
                        ->options(ContentProfile::options())
                        ->native(false)
                        ->required(),
                    TextInput::make('seo_title')->label(__('panel.fields.seo_title')),
                    Textarea::make('seo_description')->label(__('panel.fields.seo_description'))->rows(2),
                ])->columns(2),
                Section::make(__('panel.sections.account'))->schema([
                    Toggle::make('marketing_opt_in')
                        ->label(__('panel.fields.marketing_opt_in'))
                        ->helperText(__('panel.fields.marketing_opt_in_helper')),
                ]),
                Section::make(__('panel.sections.contact'))->schema([
                    TextInput::make('full_name')->label(__('panel.fields.full_name'))->required(),
                    TextInput::make('email')->label(__('panel.fields.email'))->email(),
                    TextInput::make('phone')->label(__('panel.fields.phone')),
                    TextInput::make('location')->label(__('panel.fields.location')),
                    TextInput::make('website')->label(__('panel.fields.website'))->url()->columnSpanFull(),
                    DatePicker::make('date_of_birth')->label(__('panel.fields.date_of_birth')),
                    FileUpload::make('avatar_path')
                        ->label(__('panel.fields.avatar'))
                        ->image()
                        ->disk('public')
                        ->visibility('public')
                        ->directory('avatars')
                        ->columnSpanFull(),
                ])->columns(2),
                Section::make(__('panel.sections.copy'))->schema([
                    LocaleTabs::make([
                        ['name' => 'headline', 'label' => __('panel.fields.headline'), 'required' => true],
                        ['name' => 'tagline', 'label' => __('panel.fields.tagline'), 'type' => 'textarea', 'rows' => 2],
                        ['name' => 'about', 'label' => __('panel.fields.about'), 'type' => 'editor'],
                        ['name' => 'philosophy_quote', 'label' => __('panel.fields.philosophy_quote'), 'type' => 'editor'],
                    ]),
                ]),
            ]);
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            Form::make([EmbeddedSchema::make('form')])
                ->id('form')
                ->livewireSubmitHandler('save')
                ->footer([
                    Actions::make([
                        Action::make('save')
                            ->label(__('panel.actions.save'))
                            ->submit('save'),
                    ]),
                ]),
        ]);
    }

    public function save(): void
    {
        $pendingAvatar = $this->data['avatar_path'] ?? null;
        $data = $this->form->getState();
        $user = auth()->user();
        $portfolio = $this->portfolio();

        $user->update([
            'username' => $data['username'],
            'name' => $data['full_name'],
        ]);

        $pref = UserMailPreference::forUser($user);

        if ($data['marketing_opt_in'] ?? false) {
            $pref->optInMarketing();
        } else {
            $pref->optOutMarketing();
        }

        $portfolio->update([
            'slug' => $data['username'],
            'is_published' => $data['is_published'],
            'default_locale' => $data['default_locale'],
            'default_theme' => $data['default_theme'],
            'content_profile' => ContentProfile::tryFrom((string) ($data['content_profile'] ?? ''))
                ?? ContentProfile::It,
            'seo_title' => $data['seo_title'] ?? null,
            'seo_description' => $data['seo_description'] ?? null,
        ]);

        $this->profile()->update([
            'full_name' => $data['full_name'],
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'location' => $data['location'] ?? null,
            'website' => $data['website'] ?? null,
            'date_of_birth' => $data['date_of_birth'] ?? null,
            'avatar_path' => $data['avatar_path'] ?? null,
            'headline' => $data['headline'] ?? [],
            'tagline' => $data['tagline'] ?? [],
            'about' => $data['about'] ?? [],
            'philosophy_quote' => $data['philosophy_quote'] ?? [],
        ]);

        WizardPendingAvatar::forget($portfolio->id, $pendingAvatar);

        $portfolio->refresh();
        $config = ContentProfileConfig::for($portfolio);
        $config->applySuggestedThemeIfUnset($portfolio);
        $config->syncCvSettingsForSections($portfolio);

        Notification::make()->title(__('panel.notify.saved'))->success()->send();
    }

    protected function portfolio(): Portfolio
    {
        return auth()->user()->portfolio;
    }

    protected function profile(): Profile
    {
        return $this->portfolio()->profile;
    }
}
