<?php

namespace App\Filament\Resources\Education;

use App\Filament\Concerns\TranslatesNavigation;
use App\Filament\Forms\LocaleTabs;
use App\Filament\Resources\Education\Pages\ManageEducation;
use App\Filament\Tables\Columns\LocaleTextColumn;
use App\Models\Education;
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

class EducationResource extends Resource
{
    use TranslatesNavigation;

    protected static ?string $model = Education::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedAcademicCap;

    protected static ?string $navigationLabel = 'panel.nav.education';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            LocaleTabs::make([
                ['name' => 'degree', 'label' => __('panel.fields.degree'), 'required' => true],
                ['name' => 'school', 'label' => __('panel.fields.school'), 'required' => true],
                ['name' => 'details', 'label' => __('panel.fields.details'), 'type' => 'editor'],
            ]),
            TextInput::make('period')->label(__('panel.fields.period')),
            TextInput::make('sort_order')->label(__('panel.fields.sort_order'))->numeric()->default(0),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                LocaleTextColumn::make('degree')->label(__('panel.fields.degree')),
                LocaleTextColumn::make('school')->label(__('panel.fields.school')),
                TextColumn::make('period')->label(__('panel.fields.period')),
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
            'index' => ManageEducation::route('/'),
        ];
    }
}
