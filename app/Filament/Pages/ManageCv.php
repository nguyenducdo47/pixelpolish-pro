<?php

namespace App\Filament\Pages;

use App\Filament\Concerns\TranslatesPage;
use App\Filament\Forms\LocaleTabs;
use App\Models\Cv;
use App\Models\Portfolio;
use App\Support\AppearanceTheme;
use App\Support\ContentProfileConfig;
use App\Support\LocaleCatalog;
use App\Support\UiLocale;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Arr;

class ManageCv extends Page
{
    use TranslatesPage;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static ?string $navigationLabel = 'panel.nav.cv';

    protected static ?int $navigationSort = 8;

    protected static ?string $title = 'panel.pages.cv';

    protected string $view = 'filament.pages.manage-cv';

    public ?array $data = [];

    public ?int $editingCvId = null;

    public function mount(): void
    {
        $portfolio = auth()->user()->portfolio;

        $this->editingCvId = $portfolio->defaultCv?->id
            ?? $portfolio->cvs()->orderByDesc('is_default')->value('id');

        $this->form->fill($this->activeCv()->toArray());
    }

    public function updatedEditingCvId(): void
    {
        $this->form->fill($this->activeCv()->fresh()->toArray());
    }

    public function defaultForm(Schema $schema): Schema
    {
        return $schema->statePath('data')->model($this->activeCv());
    }

    public function form(Schema $schema): Schema
    {
        $config = fn (): ContentProfileConfig => ContentProfileConfig::for(auth()->user()?->portfolio);

        return $schema->components([
            Select::make('editingCvId')
                ->label(__('panel.fields.cv_document_config'))
                ->options(fn (): array => $this->cvDocumentOptions())
                ->default(fn (): ?int => $this->editingCvId)
                ->live()
                ->native(false)
                ->required()
                ->visible(fn (): bool => auth()->user()->portfolio->cvs()->count() > 1)
                ->helperText(__('panel.fields.cv_document_config_help'))
                ->columnSpanFull()
                ->statePath('editingCvId'),
            Section::make(__('panel.sections.cv_resume_summary'))->schema([
                LocaleTabs::make([
                    [
                        'name' => 'resume_summary_title',
                        'label' => __('panel.fields.resume_summary_title'),
                        'type' => 'text',
                        'required' => false,
                    ],
                    [
                        'name' => 'resume_summary',
                        'label' => __('panel.fields.resume_summary'),
                        'type' => 'textarea',
                        'rows' => 4,
                    ],
                ]),
                Toggle::make('show_resume_summary')
                    ->label(__('panel.fields.show_resume_summary')),
            ]),
            Section::make(__('panel.sections.cv_template'))
                ->description(__('panel.sections.cv_template_scope_help'))
                ->schema([
                Select::make('template')
                    ->label(__('panel.fields.template'))
                    ->options(fn (): array => AppearanceTheme::cvLayoutOptions())
                    ->native(false)
                    ->required(),
                Toggle::make('show_avatar')
                    ->label(__('panel.fields.show_avatar'))
                    ->visible(fn (): bool => (bool) auth()->user()?->portfolio?->profile?->hasAvatar()),
                Toggle::make('show_about')->label(__('panel.fields.show_about')),
                Toggle::make('show_skills')
                    ->label(fn (): string => $config()->panelNavLabel('skills'))
                    ->visible(fn (): bool => $config()->sectionEnabled('skills')),
                Toggle::make('show_projects')
                    ->label(fn (): string => $config()->panelNavLabel('projects'))
                    ->visible(fn (): bool => $config()->sectionEnabled('projects')),
                Toggle::make('show_project_period')
                    ->label(__('panel.fields.show_project_period'))
                    ->visible(fn (): bool => $config()->sectionEnabled('projects')),
                Toggle::make('show_social_links')
                    ->label(__('panel.fields.show_social_links')),
                Toggle::make('show_certifications')
                    ->label(__('panel.fields.show_certifications')),
                Toggle::make('show_career_targets')
                    ->label(__('panel.fields.show_career_targets')),
                Toggle::make('show_strengths')
                    ->label(__('panel.fields.show_strengths')),
                Toggle::make('show_education')->label(__('panel.fields.show_education')),
                Toggle::make('show_work_experience')->label(__('panel.fields.show_work_experience')),
                Toggle::make('show_languages')->label(__('panel.fields.show_languages')),
                Toggle::make('show_principles')
                    ->label(__('panel.fields.show_principles'))
                    ->visible(fn (): bool => $config()->sectionEnabled('philosophy')),
            ])->columns(2),
        ]);
    }

    public function content(Schema $schema): Schema
    {
        $portfolio = auth()->user()->portfolio;

        return $schema->components([
            Form::make([EmbeddedSchema::make('form')])
                ->id('form')
                ->livewireSubmitHandler('save')
                ->footer([
                    Actions::make([
                        Action::make('save')->label(__('panel.actions.save'))->submit('save'),
                        $this->localeActionGroup(
                            $portfolio,
                            fn (): Cv => $this->activeCv(),
                            'preview',
                            __('panel.actions.preview_menu'),
                            Heroicon::OutlinedEye,
                            false,
                        ),
                        $this->localeActionGroup(
                            $portfolio,
                            fn (): Cv => $this->activeCv(),
                            'pdf',
                            __('panel.actions.pdf_menu'),
                            Heroicon::OutlinedArrowDownTray,
                            true,
                        ),
                    ]),
                ]),
        ]);
    }

    public function save(): void
    {
        $cv = $this->activeCv();
        $state = Arr::only($this->form->getState(), $cv->getFillable());
        $cv->update($state);

        if ($cv->is_default) {
            AppearanceTheme::syncCvLayout($cv->portfolio, (string) ($state['template'] ?? 'modern'));
        }

        Notification::make()->title(__('panel.notify.cv_saved'))->success()->send();
    }

    /**
     * @return array<int, string>
     */
    protected function cvDocumentOptions(): array
    {
        $locale = UiLocale::current();

        return auth()->user()->portfolio->cvs()
            ->orderByDesc('is_default')
            ->orderBy('slug')
            ->get()
            ->mapWithKeys(function (Cv $cv) use ($locale): array {
                $label = trim($cv->localeText('name', $locale)) ?: $cv->slug;
                if ($cv->is_default) {
                    $label .= ' · '.__('panel.fields.cv_is_default');
                }

                return [$cv->id => $label];
            })
            ->all();
    }

    protected function activeCv(): Cv
    {
        $portfolio = auth()->user()->portfolio;

        if ($this->editingCvId) {
            return $portfolio->cvs()->whereKey($this->editingCvId)->firstOrFail();
        }

        return $portfolio->defaultCv()->firstOrCreate(
            ['portfolio_id' => $portfolio->id, 'is_default' => true],
            [
                'slug' => 'default',
                'name' => ['vi' => 'CV mặc định', 'en' => 'Default CV'],
            ],
        );
    }

    protected function localeActionGroup(
        Portfolio $portfolio,
        \Closure $cvResolver,
        string $name,
        string $label,
        Heroicon $icon,
        bool $pdf,
    ): ActionGroup {
        $locales = LocaleCatalog::enabled();

        return ActionGroup::make(
            $locales->map(function ($locale) use ($portfolio, $cvResolver, $name, $pdf) {
                $cv = $cvResolver();

                $params = [
                    'locale' => $locale->code,
                    'username' => $portfolio->slug,
                ];

                if ($pdf) {
                    $route = $cv->is_default ? 'portfolio.cv.pdf' : 'portfolio.cv.pdf.show';
                    if (! $cv->is_default) {
                        $params['cv'] = $cv->slug;
                    }
                } else {
                    $route = $cv->is_default ? 'portfolio.cv' : 'portfolio.cv.show';
                    if (! $cv->is_default) {
                        $params['cv'] = $cv->slug;
                    }
                }

                return Action::make($name.'_'.$locale->code)
                    ->label($locale->native_name.' ('.strtoupper($locale->code).')')
                    ->url(route($route, $params))
                    ->openUrlInNewTab();
            })->all()
        )
            ->label($label)
            ->icon($icon)
            ->button()
            ->color('gray')
            ->dropdownPlacement('top-start')
            ->hidden($locales->isEmpty());
    }
}
