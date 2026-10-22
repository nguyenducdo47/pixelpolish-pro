<?php

namespace App\Filament\Resources\CvListItems;

use App\Enums\CvListType;
use App\Filament\Concerns\TranslatesNavigation;
use App\Filament\Forms\LocaleTabs;
use App\Filament\Resources\CvListItems\Pages\ManageCvListItems;
use App\Filament\Tables\Columns\LocaleTextColumn;
use App\Models\CvListItem;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;

class CvListItemResource extends Resource
{
    use TranslatesNavigation;

    protected static ?string $model = CvListItem::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static ?string $navigationLabel = 'panel.nav.cv_lists';

    protected static ?int $navigationSort = 7;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('type')
                ->label(__('panel.fields.cv_list_type'))
                ->options(CvListType::options())
                ->native(false)
                ->required(),
            LocaleTabs::make([
                ['name' => 'title', 'label' => __('panel.fields.cv_list_title'), 'required' => true],
            ]),
            \Filament\Forms\Components\TextInput::make('sort_order')
                ->label(__('panel.fields.sort_order'))
                ->numeric()
                ->default(0),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('type')
                    ->label(__('panel.fields.cv_list_type'))
                    ->formatStateUsing(fn ($state) => $state instanceof CvListType ? $state->label() : CvListType::tryFrom((string) $state)?->label()),
                LocaleTextColumn::make('title')->label(__('panel.fields.cv_list_title')),
            ])
            ->defaultSort('sort_order')
            ->filters([
                SelectFilter::make('type')
                    ->label(__('panel.fields.cv_list_type'))
                    ->options(CvListType::options()),
            ])
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
            'index' => ManageCvListItems::route('/'),
        ];
    }
}
