<?php

namespace App\Filament\Resources\Locales;

use App\Filament\Concerns\TranslatesNavigation;
use App\Filament\Resources\Locales\Pages\ManageLocales;
use App\Models\Locale;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class LocaleResource extends Resource
{
    use TranslatesNavigation;

    protected static ?string $model = Locale::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedLanguage;

    protected static ?string $navigationLabel = 'panel.nav.site_locales';

    protected static string|UnitEnum|null $navigationGroup = 'panel.nav.settings';

    protected static ?int $navigationSort = 99;

    protected static ?string $recordTitleAttribute = 'code';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('code')
                ->label(__('panel.fields.code'))
                ->required()
                ->maxLength(12)
                ->unique(ignoreRecord: true)
                ->helperText(__('panel.fields.code_helper'))
                ->regex('/^[a-z]{2}(?:-[a-z]{2})?$/')
                ->dehydrateStateUsing(fn (?string $state) => strtolower(trim((string) $state))),
            TextInput::make('name')->label(__('panel.fields.english_name'))->required()->helperText(__('panel.fields.english_name_helper')),
            TextInput::make('native_name')->label(__('panel.fields.native'))->required()->helperText(__('panel.fields.native_name_helper')),
            TextInput::make('sort_order')->label(__('panel.fields.sort_order'))->numeric()->default(0),
            Toggle::make('is_enabled')->label(__('panel.fields.is_enabled'))->default(true),
            Toggle::make('is_default')->label(__('panel.fields.is_default'))->helperText(__('panel.fields.is_default_helper')),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')->label(__('panel.fields.code'))->badge()->searchable(),
                TextColumn::make('native_name')->label(__('panel.fields.native')),
                TextColumn::make('name')->label(__('panel.fields.english_name')),
                IconColumn::make('is_enabled')->label(__('panel.fields.is_enabled'))->boolean(),
                IconColumn::make('is_default')->label(__('panel.fields.is_default'))->boolean(),
                TextColumn::make('sort_order')->label(__('panel.fields.sort_order')),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()
                    ->disabled(fn (Locale $record) => $record->is_default || Locale::query()->count() <= 1),
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

    public static function getPages(): array
    {
        return [
            'index' => ManageLocales::route('/'),
        ];
    }
}
