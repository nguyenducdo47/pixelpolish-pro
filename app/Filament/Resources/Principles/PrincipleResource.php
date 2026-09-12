<?php

namespace App\Filament\Resources\Principles;

use App\Filament\Concerns\TranslatesNavigation;
use App\Filament\Forms\LocaleTabs;
use App\Filament\Resources\Principles\Pages\ManagePrinciples;
use App\Filament\Tables\Columns\LocaleTextColumn;
use App\Models\Principle;
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

class PrincipleResource extends Resource
{
    use TranslatesNavigation;

    protected static ?string $model = Principle::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedLightBulb;

    protected static ?string $navigationLabel = 'panel.nav.philosophy';

    protected static ?int $navigationSort = 6;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            LocaleTabs::make([
                ['name' => 'title', 'label' => __('panel.fields.title'), 'required' => true],
                ['name' => 'description', 'label' => __('panel.fields.description'), 'type' => 'editor'],
            ]),
            TextInput::make('sort_order')->label(__('panel.fields.sort_order'))->numeric()->default(0),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                LocaleTextColumn::make('title')->label(__('panel.fields.title')),
                LocaleTextColumn::make('description')->label(__('panel.fields.description'))->formatStateUsing(fn (?string $state) => str($state)->stripTags()->limit(60))->limit(60),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
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
            'index' => ManagePrinciples::route('/'),
        ];
    }
}
