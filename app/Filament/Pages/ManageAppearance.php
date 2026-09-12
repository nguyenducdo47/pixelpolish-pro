<?php

namespace App\Filament\Pages;

use App\Filament\Concerns\TranslatesPage;
use App\Filament\Forms\ThemeAppearanceFields;
use App\Models\Portfolio;
use App\Models\Theme;
use App\Support\AppearanceTheme;
use App\Support\LocaleCatalog;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use InvalidArgumentException;

class ManageAppearance extends Page
{
    use TranslatesPage;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedPaintBrush;

    protected static ?string $navigationLabel = 'panel.nav.appearance';

    protected static ?int $navigationSort = 9;

    protected static ?string $title = 'panel.pages.appearance';

    protected string $view = 'filament.pages.manage-appearance';

    public ?array $data = [];

    public static function canAccess(): bool
    {
        return auth()->user()?->portfolio !== null;
    }

    public function mount(): void
    {
        $this->form->fill($this->formState());
    }

    public function defaultForm(Schema $schema): Schema
    {
        return $schema->statePath('data')->model($this->portfolio());
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('panel.sections.theme_presets'))
                ->description(__('panel.sections.theme_presets_help'))
                ->schema([
                    Select::make('theme_id')
                        ->label(__('panel.fields.theme_preset'))
                        ->options(fn (): array => Theme::enabledOptions())
                        ->live()
                        ->afterStateUpdated(function (mixed $state, Set $set): void {
                            if (! is_numeric($state)) {
                                return;
                            }

                            $this->fillFromCatalog($set, (int) $state);
                        })
                        ->required()
                        ->helperText(__('panel.fields.theme_user_helper')),
                ]),
            Section::make(__('panel.sections.theme_layout'))
                ->schema(ThemeAppearanceFields::layout())
                ->columns(2),
            Section::make(__('panel.sections.theme_colors'))
                ->schema(ThemeAppearanceFields::colors('colors'))
                ->columns(4),
            Section::make(__('panel.sections.theme_dark_colors'))
                ->schema(ThemeAppearanceFields::colors('dark'))
                ->columns(4)
                ->collapsed(),
        ]);
    }

    public function content(Schema $schema): Schema
    {
        $portfolio = $this->portfolio();

        return $schema->components([
            Form::make([EmbeddedSchema::make('form')])
                ->id('form')
                ->livewireSubmitHandler('save')
                ->footer([
                    Actions::make([
                        Action::make('save')->label(__('panel.actions.save'))->submit('save'),
                        Action::make('applyPreset')
                            ->label(__('panel.actions.apply_theme'))
                            ->color('gray')
                            ->action(fn () => $this->applySelectedTheme()),
                        $this->localeActionGroup(
                            $portfolio,
                            'preview_site',
                            __('panel.actions.view_site'),
                            Heroicon::OutlinedGlobeAlt,
                            'portfolio.show',
                        ),
                        $this->localeActionGroup(
                            $portfolio,
                            'preview_cv',
                            __('panel.actions.preview_menu'),
                            Heroicon::OutlinedEye,
                            'portfolio.cv',
                        ),
                    ]),
                ]),
        ]);
    }

    /**
     * @return array<Action>
     */
    protected function getHeaderActions(): array
    {
        return [
            Action::make('import')
                ->label(__('panel.actions.import_theme'))
                ->icon(Heroicon::OutlinedArrowUpTray)
                ->form([
                    Textarea::make('json')
                        ->label(__('panel.fields.theme_json'))
                        ->helperText(__('panel.fields.theme_json_user_helper'))
                        ->rows(12)
                        ->required(),
                ])
                ->action(function (array $data): void {
                    try {
                        $imported = AppearanceTheme::import((string) ($data['json'] ?? ''));
                    } catch (InvalidArgumentException $e) {
                        Notification::make()->title($e->getMessage())->danger()->send();

                        return;
                    }

                    $imported['theme_id'] = $this->data['theme_id'] ?? AppearanceTheme::assignedTheme($this->portfolio())?->id;
                    $theme = AppearanceTheme::apply($this->portfolio(), $imported);
                    $this->form->fill($this->formState());
                    Notification::make()->title(__('panel.notify.theme_imported'))->success()->send();
                    $this->data = [...$theme, 'theme_id' => $imported['theme_id']];
                }),
            Action::make('export')
                ->label(__('panel.actions.export_theme'))
                ->icon(Heroicon::OutlinedArrowDownTray)
                ->action(function () {
                    $payload = AppearanceTheme::export(AppearanceTheme::resolve($this->portfolio()));

                    return response()->streamDownload(
                        fn () => print (json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)),
                        'portfotilo-theme.json',
                        ['Content-Type' => 'application/json'],
                    );
                }),
            Action::make('reset')
                ->label(__('panel.actions.reset_theme'))
                ->color('gray')
                ->requiresConfirmation()
                ->action(function (): void {
                    $catalog = AppearanceTheme::assignedTheme($this->portfolio());

                    if ($catalog) {
                        $this->portfolio()->themeCustomizations()->where('theme_id', $catalog->id)->delete();
                    }

                    $this->form->fill($this->formState());
                    Notification::make()->title(__('panel.notify.theme_reset'))->success()->send();
                }),
        ];
    }

    public function save(): void
    {
        $this->persist($this->form->getState());
        Notification::make()->title(__('panel.notify.theme_saved'))->success()->send();
    }

    public function applySelectedTheme(): void
    {
        $this->persist($this->form->getState());
        $this->form->fill($this->formState());
        Notification::make()->title(__('panel.notify.theme_applied'))->success()->send();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function persist(array $data): void
    {
        $theme = AppearanceTheme::apply($this->portfolio(), $data);
        $this->data = [...$theme, 'theme_id' => $this->portfolio()->fresh()->theme_id];
    }

    /**
     * @return array<string, mixed>
     */
    protected function formState(): array
    {
        $portfolio = $this->portfolio();
        $catalog = AppearanceTheme::assignedTheme($portfolio);
        $resolved = AppearanceTheme::resolve($portfolio);

        return [
            ...$resolved,
            'theme_id' => $catalog?->id,
        ];
    }

    protected function fillFromCatalog(Set $set, int $themeId): void
    {
        $catalog = Theme::query()->where('id', $themeId)->where('is_enabled', true)->first();

        if (! $catalog) {
            return;
        }

        $custom = $this->portfolio()->themeCustomizations()->where('theme_id', $catalog->id)->value('customization');
        $theme = AppearanceTheme::normalize(is_array($custom) ? $custom : [], $catalog->definition());

        foreach (['layout', 'hero', 'radius', 'font', 'density', 'cv_layout', 'show_particles'] as $key) {
            $set($key, $theme[$key]);
        }

        foreach (AppearanceTheme::colorKeys() as $key) {
            $set('colors.'.$key, $theme['colors'][$key]);
            $set('dark.'.$key, $theme['dark'][$key]);
        }
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

    protected function portfolio(): Portfolio
    {
        return auth()->user()->portfolio;
    }
}
