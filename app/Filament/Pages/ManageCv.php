<?php

namespace App\Filament\Pages;

use App\Filament\Concerns\TranslatesPage;
use App\Models\CvSetting;
use App\Models\Portfolio;
use App\Support\AppearanceTheme;
use App\Support\LocaleCatalog;
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

class ManageCv extends Page
{
    use TranslatesPage;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static ?string $navigationLabel = 'panel.nav.cv';

    protected static ?int $navigationSort = 8;

    protected static ?string $title = 'panel.pages.cv';

    protected string $view = 'filament.pages.manage-cv';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill($this->settings()->toArray());
    }

    public function defaultForm(Schema $schema): Schema
    {
        return $schema->statePath('data')->model($this->settings());
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('panel.sections.cv_template'))->schema([
                Select::make('template')
                    ->label(__('panel.fields.template'))
                    ->options(fn (): array => AppearanceTheme::cvLayoutOptions())
                    ->native(false)
                    ->required(),
                Toggle::make('show_avatar')
                    ->label(__('panel.fields.show_avatar'))
                    ->visible(fn (): bool => (bool) auth()->user()?->portfolio?->profile?->hasAvatar()),
                Toggle::make('show_about')->label(__('panel.fields.show_about')),
                Toggle::make('show_skills')->label(__('panel.fields.show_skills')),
                Toggle::make('show_projects')->label(__('panel.fields.show_projects')),
                Toggle::make('show_education')->label(__('panel.fields.show_education')),
                Toggle::make('show_languages')->label(__('panel.fields.show_languages')),
                Toggle::make('show_principles')->label(__('panel.fields.show_principles')),
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
                            'preview',
                            __('panel.actions.preview_menu'),
                            Heroicon::OutlinedEye,
                            'portfolio.cv',
                        ),
                        $this->localeActionGroup(
                            $portfolio,
                            'pdf',
                            __('panel.actions.pdf_menu'),
                            Heroicon::OutlinedArrowDownTray,
                            'portfolio.cv.pdf',
                        ),
                    ]),
                ]),
        ]);
    }

    public function save(): void
    {
        $state = $this->form->getState();
        $this->settings()->update($state);
        AppearanceTheme::syncCvLayout($this->settings()->portfolio, (string) ($state['template'] ?? 'modern'));
        Notification::make()->title(__('panel.notify.cv_saved'))->success()->send();
    }

    protected function localeActionGroup(
        Portfolio $portfolio,
        string $name,
        string $label,
        Heroicon $icon,
        string $route,
    ): ActionGroup {
        $locales = LocaleCatalog::enabled();

        return ActionGroup::make(
            $locales->map(fn ($locale) => Action::make($name.'_'.$locale->code)
                ->label($locale->native_name.' ('.strtoupper($locale->code).')')
                ->url(route($route, [
                    'locale' => $locale->code,
                    'username' => $portfolio->slug,
                ]))
                ->openUrlInNewTab())->all()
        )
            ->label($label)
            ->icon($icon)
            ->button()
            ->color('gray')
            ->dropdownPlacement('top-start')
            ->hidden($locales->isEmpty());
    }

    protected function settings(): CvSetting
    {
        return auth()->user()->portfolio->cvSettings;
    }
}
