<?php

namespace App\Filament\Resources\TranslationApis;

use App\Filament\Concerns\TranslatesNavigation;
use App\Filament\Resources\TranslationApis\Pages\ManageTranslationApis;
use App\Models\TranslationApi;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class TranslationApiResource extends Resource
{
    use TranslatesNavigation;

    protected static ?string $model = TranslationApi::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedGlobeAlt;

    protected static ?string $navigationLabel = 'panel.nav.translation_apis';

    protected static string|UnitEnum|null $navigationGroup = 'panel.nav.settings';

    protected static ?int $navigationSort = 85;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')
                ->label(__('panel.fields.api_name'))
                ->required()
                ->maxLength(80),
            Select::make('driver')
                ->label(__('panel.fields.api_driver'))
                ->options(fn (): array => TranslationApi::driverOptions())
                ->required()
                ->helperText(__('panel.fields.api_driver_helper')),
            Select::make('method')
                ->label(__('panel.fields.api_method'))
                ->options([
                    'GET' => 'GET',
                    'POST' => 'POST',
                ])
                ->required()
                ->default('GET'),
            TextInput::make('url')
                ->label(__('panel.fields.api_url'))
                ->url()
                ->required()
                ->columnSpanFull(),
            TextInput::make('user_agent')
                ->label(__('panel.fields.api_user_agent'))
                ->maxLength(255)
                ->columnSpanFull(),
            TextInput::make('sort_order')
                ->label(__('panel.fields.sort_order'))
                ->numeric()
                ->default(0),
            Toggle::make('is_enabled')
                ->label(__('panel.fields.is_enabled'))
                ->default(true),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label(__('panel.fields.api_name'))->searchable(),
                TextColumn::make('driver')
                    ->label(__('panel.fields.api_driver'))
                    ->formatStateUsing(fn (string $state): string => TranslationApi::driverOptions()[$state] ?? $state)
                    ->badge(),
                TextColumn::make('method')->label(__('panel.fields.api_method')),
                TextColumn::make('url')->label(__('panel.fields.api_url'))->limit(48),
                IconColumn::make('is_enabled')->label(__('panel.fields.is_enabled'))->boolean(),
                TextColumn::make('sort_order')->label(__('panel.fields.sort_order')),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->deselectRecordsAfterCompletion(),
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
            'index' => ManageTranslationApis::route('/'),
        ];
    }
}
