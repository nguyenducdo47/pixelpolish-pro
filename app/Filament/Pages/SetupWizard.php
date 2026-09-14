<?php

namespace App\Filament\Pages;

use App\Filament\Concerns\TranslatesPage;
use App\Filament\Forms\LocaleTabs;
use App\Filament\Support\ProjectFormSchema;
use App\Enums\ContentProfile;
use App\Models\Portfolio;
use App\Services\PortfolioWizardSync;
use App\Support\AppearanceTheme;
use App\Support\ContentProfileConfig;
use App\Support\LocaleCatalog;
use App\Support\UiLocale;
use App\Support\WizardPendingAvatar;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ViewField;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
use Illuminate\Validation\Rule;

class SetupWizard extends Page
{
    use TranslatesPage;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedSparkles;

    protected static ?string $navigationLabel = 'panel.nav.setup';

    protected static ?int $navigationSort = -2;

    protected static ?string $slug = 'setup';

    protected static ?string $title = 'panel.pages.setup';

    protected string $view = 'filament.pages.setup-wizard';

    public ?array $data = [];

    public function mount(PortfolioWizardSync $sync): void
    {
        $this->form->fill($sync->formState($this->portfolio()));

        $welcome = session()->pull('content_profile_welcome');

        if (is_string($welcome) && $welcome !== '') {
            Notification::make()
                ->title(__('panel.notify.content_profile_welcome', ['profile' => $welcome]))
                ->success()
                ->send();
        }
    }

    public function defaultForm(Schema $schema): Schema
    {
        return $schema->statePath('data')->model($this->portfolio());
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Wizard::make($this->wizardSteps())
                ->skippable()
                ->persistStepInQueryString()
                ->extraAttributes(['class' => 'setup-wizard'])
                ->submitAction(new HtmlString(Blade::render(
                    '<x-filament::button type="submit" size="sm">{{ $label }}</x-filament::button>',
                    ['label' => __('panel.wizard.finish')]
                )))
                ->columnSpanFull(),
        ]);
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            Form::make([EmbeddedSchema::make('form')])
                ->id('form')
                ->livewireSubmitHandler('save'),
        ]);
    }

    public function save(PortfolioWizardSync $sync): void
    {
        $sync->saveAll($this->portfolio(), $this->persistableData(commitAvatar: true));
        $this->form->fill($sync->formState($this->portfolio()->refresh()));

        Notification::make()->title(__('panel.notify.wizard_saved'))->success()->send();
    }

    public function persistStep(PortfolioWizardSync $sync): void
    {
        $sync->saveAll($this->portfolio(), $this->persistableData());
    }

    /**
     * @return array<string, mixed>
     */
    protected function persistableData(bool $commitAvatar = false): array
    {
        $state = $this->data ?? [];
        $portfolioId = $this->portfolio()->id;
        $avatar = $state['avatar_path'] ?? null;

        WizardPendingAvatar::remember($portfolioId, $avatar);

        if (! $commitAvatar) {
            unset($state['avatar_path']);

            return $state;
        }

        $state['avatar_path'] = WizardPendingAvatar::commit($portfolioId, $avatar);

        return $state;
    }

    /**
     * @return array<string, mixed>
     */
    public function previewUrls(): array
    {
        return once(function (): array {
            app(PortfolioWizardSync::class)->saveAll($this->portfolio(), $this->persistableData());

            $portfolio = $this->portfolio()->fresh();
            $locale = $portfolio->default_locale ?: LocaleCatalog::defaultCode();

            return [
                'site' => $portfolio->publicUrl($locale),
                'cv' => $portfolio->cvUrl($locale),
            ];
        });
    }

    protected function profileStep(): Step
    {
        return Step::make($this->contentConfig()->wizardLabel('profile'))
            ->description($this->contentConfig()->wizardDescription('profile'))
            ->icon(Heroicon::OutlinedUserCircle)
            ->afterValidation(fn (PortfolioWizardSync $sync) => $this->persistStep($sync))
            ->schema([
                TextInput::make('username')
                    ->label(__('panel.fields.username'))
                    ->required()
                    ->alphaDash()
                    ->helperText(__('panel.fields.username_helper'))
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
                    ->helperText(__('panel.fields.content_profile_helper'))
                    ->options(ContentProfile::options())
                    ->native(false)
                    ->required()
                    ->live(),
                TextInput::make('seo_title')->label(__('panel.fields.seo_title')),
                Textarea::make('seo_description')->label(__('panel.fields.seo_description'))->rows(2),
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
                    ->storeFiles(false)
                    ->columnSpanFull(),
                LocaleTabs::make([
                    ['name' => 'headline', 'label' => __('panel.fields.headline'), 'required' => true],
                    ['name' => 'tagline', 'label' => __('panel.fields.tagline'), 'type' => 'textarea', 'rows' => 2],
                    ['name' => 'about', 'label' => __('panel.fields.about'), 'type' => 'editor'],
                    ['name' => 'philosophy_quote', 'label' => __('panel.fields.philosophy_quote'), 'type' => 'editor'],
                ]),
            ])->columns(2);
    }

    protected function skillsStep(): Step
    {
        return Step::make($this->contentConfig()->wizardLabel('skills'))
            ->description($this->contentConfig()->wizardDescription('skills'))
            ->icon(Heroicon::OutlinedSparkles)
            ->afterValidation(fn (PortfolioWizardSync $sync) => $this->persistStep($sync))
            ->schema([
                Repeater::make('skill_categories')
                    ->label(fn (): string => $this->contentConfig()->panelNavLabel('skills'))
                    ->defaultItems(0)
                    ->collapsed()
                    ->cloneable()
                    ->itemLabel(fn (array $state): ?string => $this->bagLabel($state['name'] ?? null) ?: null)
                    ->addActionLabel(__('panel.wizard.add_skill_category'))
                    ->schema([
                        Hidden::make('id'),
                        LocaleTabs::make([
                            ['name' => 'name', 'label' => __('panel.fields.category_name'), 'required' => true],
                        ]),
                        Repeater::make('skills')
                            ->label(__('panel.fields.skills'))
                            ->defaultItems(0)
                            ->collapsed()
                            ->itemLabel(fn (array $state): ?string => $state['name'] ?? null)
                            ->addActionLabel(__('panel.wizard.add_skill'))
                            ->schema([
                                Hidden::make('id'),
                                TextInput::make('name')->label(__('panel.fields.name'))->required(),
                                TextInput::make('level')
                                    ->label(__('panel.fields.level'))
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxValue(100)
                                    ->default(70)
                                    ->visible(fn (Get $get): bool => ProjectFormSchema::skillLevelVisible(
                                        $get,
                                        fn (): mixed => $this->data['content_profile'] ?? $this->portfolio()->content_profile,
                                    )),
                                LocaleTabs::make([
                                    ['name' => 'description', 'label' => __('panel.fields.description'), 'type' => 'editor'],
                                ]),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    protected function projectsStep(): Step
    {
        return Step::make($this->contentConfig()->wizardLabel('projects'))
            ->description($this->contentConfig()->wizardDescription('projects'))
            ->icon(Heroicon::OutlinedBriefcase)
            ->afterValidation(fn (PortfolioWizardSync $sync) => $this->persistStep($sync))
            ->schema([
                Repeater::make('projects')
                    ->label(fn (): string => $this->contentConfig()->panelNavLabel('projects'))
                    ->defaultItems(0)
                    ->collapsed()
                    ->cloneable()
                    ->itemLabel(fn (array $state): ?string => $this->bagLabel($state['title'] ?? null) ?: null)
                    ->addActionLabel(__('panel.wizard.add_project'))
                    ->schema([
                        Hidden::make('id'),
                        LocaleTabs::make(ProjectFormSchema::localeTabFieldDefinitions(
                            fn (): mixed => $this->data['content_profile'] ?? $this->portfolio()->content_profile,
                        )),
                        ...ProjectFormSchema::extraFields(
                            fn (): mixed => $this->data['content_profile'] ?? $this->portfolio()->content_profile,
                        ),
                        Toggle::make('is_featured')->label(__('panel.fields.featured'))->default(true),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    protected function backgroundStep(): Step
    {
        return Step::make(__('panel.wizard.background'))
            ->description(__('panel.wizard.background_desc'))
            ->icon(Heroicon::OutlinedAcademicCap)
            ->afterValidation(fn (PortfolioWizardSync $sync) => $this->persistStep($sync))
            ->schema([
                Repeater::make('education')
                    ->label(__('panel.nav.education'))
                    ->defaultItems(0)
                    ->collapsed()
                    ->itemLabel(fn (array $state): ?string => $this->bagLabel($state['degree'] ?? null) ?: null)
                    ->addActionLabel(__('panel.wizard.add_education'))
                    ->schema([
                        Hidden::make('id'),
                        LocaleTabs::make([
                            ['name' => 'degree', 'label' => __('panel.fields.degree'), 'required' => true],
                            ['name' => 'school', 'label' => __('panel.fields.school'), 'required' => true],
                            ['name' => 'details', 'label' => __('panel.fields.details'), 'type' => 'editor'],
                        ]),
                        TextInput::make('period')->label(__('panel.fields.period')),
                    ]),
                Repeater::make('languages')
                    ->label(__('panel.nav.languages'))
                    ->defaultItems(0)
                    ->itemLabel(fn (array $state): ?string => $this->bagLabel($state['name'] ?? null) ?: null)
                    ->addActionLabel(__('panel.wizard.add_language'))
                    ->schema([
                        Hidden::make('id'),
                        LocaleTabs::make([
                            ['name' => 'name', 'label' => __('panel.fields.language'), 'required' => true],
                            ['name' => 'level', 'label' => __('panel.fields.level')],
                        ]),
                    ]),
            ]);
    }

    protected function presenceStep(): Step
    {
        return Step::make(__('panel.wizard.presence'))
            ->description(__('panel.wizard.presence_desc'))
            ->icon(Heroicon::OutlinedShare)
            ->afterValidation(fn (PortfolioWizardSync $sync) => $this->persistStep($sync))
            ->schema([
                Repeater::make('social_links')
                    ->label(__('panel.nav.social'))
                    ->defaultItems(0)
                    ->itemLabel(fn (array $state): ?string => $state['platform'] ?? null)
                    ->addActionLabel(__('panel.wizard.add_social'))
                    ->schema([
                        Hidden::make('id'),
                        Select::make('platform')
                            ->label(__('panel.fields.platform'))
                            ->options([
                                'github' => 'GitHub',
                                'linkedin' => 'LinkedIn',
                                'facebook' => 'Facebook',
                                'twitter' => 'Twitter / X',
                                'instagram' => 'Instagram',
                                'telegram' => 'Telegram',
                                'zalo' => 'Zalo',
                                'website' => 'Website',
                                'other' => __('panel.fields.other'),
                            ])
                            ->native(false)
                            ->required(),
                        TextInput::make('url')->label(__('panel.fields.url'))->url()->required(),
                    ])
                    ->columns(2),
                Repeater::make('principles')
                    ->label(__('panel.nav.philosophy'))
                    ->defaultItems(0)
                    ->collapsed()
                    ->itemLabel(fn (array $state): ?string => $this->bagLabel($state['title'] ?? null) ?: null)
                    ->addActionLabel(__('panel.wizard.add_principle'))
                    ->visible(fn (): bool => $this->contentConfig()->sectionEnabled('philosophy'))
                    ->schema([
                        Hidden::make('id'),
                        LocaleTabs::make([
                            ['name' => 'title', 'label' => __('panel.fields.title'), 'required' => true],
                            ['name' => 'description', 'label' => __('panel.fields.description'), 'type' => 'editor'],
                        ]),
                    ]),
            ]);
    }

    protected function cvStep(): Step
    {
        return Step::make(__('panel.wizard.cv'))
            ->description(__('panel.wizard.cv_desc'))
            ->icon(Heroicon::OutlinedDocumentText)
            ->afterValidation(fn (PortfolioWizardSync $sync) => $this->persistStep($sync))
            ->schema([
                Select::make('cv_template')
                    ->label(__('panel.fields.template'))
                    ->options(fn (): array => AppearanceTheme::cvLayoutOptions())
                    ->native(false)
                    ->required(),
                Toggle::make('show_avatar')
                    ->label(__('panel.fields.show_avatar'))
                    ->visible(fn (): bool => filled($this->data['avatar_path'] ?? null)
                        || WizardPendingAvatar::current($this->portfolio()->id) !== null
                        || (bool) $this->portfolio()->profile?->hasAvatar()),
                Toggle::make('show_about')->label(__('panel.fields.show_about')),
                Toggle::make('show_skills')
                    ->label(fn (): string => $this->contentConfig()->panelNavLabel('skills'))
                    ->visible(fn (): bool => $this->contentConfig()->sectionEnabled('skills')),
                Toggle::make('show_projects')
                    ->label(fn (): string => $this->contentConfig()->panelNavLabel('projects'))
                    ->visible(fn (): bool => $this->contentConfig()->sectionEnabled('projects')),
                Toggle::make('show_education')->label(__('panel.fields.show_education')),
                Toggle::make('show_languages')->label(__('panel.fields.show_languages')),
                Toggle::make('show_principles')
                    ->label(__('panel.fields.show_principles'))
                    ->visible(fn (): bool => $this->contentConfig()->sectionEnabled('philosophy')),
            ])->columns(2);
    }

    protected function previewStep(): Step
    {
        return Step::make(__('panel.wizard.preview'))
            ->description(__('panel.wizard.preview_desc'))
            ->icon(Heroicon::OutlinedEye)
            ->schema([
                Toggle::make('is_published')
                    ->label(__('panel.fields.publish_now'))
                    ->helperText(__('panel.wizard.publish_help')),
                ViewField::make('preview_frame')
                    ->label('')
                    ->dehydrated(false)
                    ->view('filament.pages.setup-preview')
                    ->columnSpanFull(),
            ]);
    }

    protected function bagLabel(mixed $bag): string
    {
        if (! is_array($bag)) {
            return '';
        }

        $locale = UiLocale::current();
        $value = $bag[$locale] ?? $bag['vi'] ?? reset($bag);

        return is_string($value) ? $value : '';
    }

    protected function portfolio(): Portfolio
    {
        return auth()->user()->portfolio;
    }

    protected function contentConfig(): ContentProfileConfig
    {
        return ContentProfileConfig::forValue($this->data['content_profile'] ?? $this->portfolio()->content_profile);
    }

    /**
     * @return array<int, Step>
     */
    protected function wizardSteps(): array
    {
        $order = ContentProfileConfig::for($this->portfolio())->wizardStepOrder();

        if (filled($this->data['content_profile'] ?? null)) {
            $order = ContentProfileConfig::forValue($this->data['content_profile'])->wizardStepOrder();
        }

        $builders = [
            'profile' => fn (): Step => $this->profileStep(),
            'skills' => fn (): Step => $this->skillsStep(),
            'projects' => fn (): Step => $this->projectsStep(),
            'background' => fn (): Step => $this->backgroundStep(),
            'presence' => fn (): Step => $this->presenceStep(),
            'cv' => fn (): Step => $this->cvStep(),
            'preview' => fn (): Step => $this->previewStep(),
        ];

        $steps = [];

        foreach ($order as $key) {
            if (isset($builders[$key])) {
                $steps[] = $builders[$key]();
            }
        }

        return $steps !== [] ? $steps : array_map(fn (callable $builder) => $builder(), array_values($builders));
    }
}
