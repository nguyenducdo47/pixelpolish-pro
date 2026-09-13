<?php

namespace App\Filament\Resources\Themes;

use App\Filament\Concerns\ConfiguresAdminTables;
use App\Filament\Concerns\TranslatesNavigation;
use App\Filament\Forms\ThemeAppearanceFields;
use App\Filament\Resources\Themes\Pages\ManageThemes;
use App\Models\Theme;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class ThemeResource extends Resource
{
    use ConfiguresAdminTables;
    use TranslatesNavigation;

    protected static ?string $model = Theme::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSwatch;

    protected static ?string $navigationLabel = 'panel.nav.themes';

    protected static string|UnitEnum|null $navigationGroup = 'panel.nav.settings';

    protected static ?int $navigationSort = 86;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('panel.sections.theme_catalog'))->schema([
                TextInput::make('name')
                    ->label(__('panel.fields.theme_name'))
                    ->required()
                    ->maxLength(80),
                TextInput::make('slug')
                    ->label(__('panel.fields.theme_slug'))
                    ->maxLength(80)
                    ->unique(ignoreRecord: true)
                    ->helperText(__('panel.fields.theme_slug_helper')),
                TextInput::make('sort_order')
                    ->label(__('panel.fields.sort_order'))
                    ->numeric()
                    ->default(0),
                Toggle::make('is_enabled')
                    ->label(__('panel.fields.is_enabled'))
                    ->default(true)
                    ->helperText(__('panel.fields.theme_enabled_helper')),
                Toggle::make('is_default')
                    ->label(__('panel.fields.is_default'))
                    ->helperText(__('panel.fields.theme_default_helper')),
            ])->columns(2),
            Section::make(__('panel.sections.theme_layout'))
                ->schema(ThemeAppearanceFields::layout())
                ->columns(2),
            Section::make(__('panel.sections.theme_colors'))
                ->schema(ThemeAppearanceFields::colors('colors'))
                ->columns(4),
            Section::make(__('panel.sections.theme_dark_colors'))
                ->schema(ThemeAppearanceFields::colors('dark'))
                ->columns(4),
        ]);
    }

    public static function configureFormModal(Action $action): Action
    {
        return $action
            ->modalWidth(Width::SevenExtraLarge)
            ->stickyModalHeader()
            ->stickyModalFooter();
    }

    public static function table(Table $table): Table
    {
        return static::configureAdminListingTable($table)
            ->columns([
                TextColumn::make('name')->label(__('panel.fields.theme_name'))->searchable()->sortable(),
                TextColumn::make('slug')->label(__('panel.fields.theme_slug'))->badge()->toggleable(),
                ColorColumn::make('colors.primary')->label(__('panel.fields.color_primary'))->toggleable(isToggledHiddenByDefault: true),
                ToggleColumn::make('is_enabled')
                    ->label(__('panel.fields.is_enabled'))
                    ->disabled(fn (Theme $record): bool => $record->is_default || ! $record->canBeDisabled())
                    ->tooltip(fn (Theme $record): string => $record->is_default || ! $record->canBeDisabled()
                        ? __('panel.fields.theme_toggle_locked')
                        : __('panel.fields.theme_enabled_helper')),
                IconColumn::make('is_default')->label(__('panel.fields.is_default'))->boolean()->toggleable(),
                TextColumn::make('sort_order')->label(__('panel.fields.sort_order'))->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('is_enabled')->label(__('panel.fields.is_enabled'))->nullable(),
                TernaryFilter::make('is_default')->label(__('panel.fields.is_default'))->nullable(),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->recordActions([
                static::configureFormModal(EditAction::make()),
                DeleteAction::make()
                    ->disabled(fn (Theme $record): bool => ! $record->canBeDeleted()),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->deselectRecordsAfterCompletion(),
                ]),
            ]);
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->isAdmin() === true
            && ! session()->has('impersonator_id')
            && Filament::getCurrentPanel()?->getId() === 'admin';
    }

    public static function canDelete(Model $record): bool
    {
        return $record instanceof Theme && $record->canBeDeleted();
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageThemes::route('/'),
        ];
    }
}
