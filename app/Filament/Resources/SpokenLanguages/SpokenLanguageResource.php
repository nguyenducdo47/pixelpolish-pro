<?php

namespace App\Filament\Resources\SpokenLanguages;

use App\Filament\Concerns\TranslatesNavigation;
use App\Filament\Forms\LocaleTabs;
use App\Filament\Resources\SpokenLanguages\Pages\ManageSpokenLanguages;
use App\Models\SpokenLanguage;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SpokenLanguageResource extends Resource
{
    use TranslatesNavigation;

    protected static ?string $model = SpokenLanguage::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedLanguage;

    protected static ?string $navigationLabel = 'panel.nav.languages';

    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            LocaleTabs::make([
                ['name' => 'name', 'label' => __('panel.fields.language'), 'required' => true],
                ['name' => 'level', 'label' => __('panel.fields.level')],
            ]),
            TextInput::make('sort_order')->label(__('panel.fields.sort_order'))->numeric()->default(0),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label(__('panel.fields.language'))->formatStateUsing(fn (SpokenLanguage $record) => $record->localeText('name')),
                TextColumn::make('level')->label(__('panel.fields.level'))->formatStateUsing(fn (SpokenLanguage $record) => $record->localeText('level')),
            ])
            ->defaultSort('sort_order')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageSpokenLanguages::route('/'),
        ];
    }
}
