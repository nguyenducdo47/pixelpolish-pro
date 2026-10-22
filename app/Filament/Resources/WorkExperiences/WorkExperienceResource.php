<?php

namespace App\Filament\Resources\WorkExperiences;

use App\Filament\Concerns\TranslatesNavigation;
use App\Filament\Forms\LocaleTabs;
use App\Filament\Resources\WorkExperiences\Pages\ManageWorkExperiences;
use App\Filament\Tables\Columns\LocaleTextColumn;
use App\Models\WorkExperience;
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

class WorkExperienceResource extends Resource
{
    use TranslatesNavigation;

    protected static ?string $model = WorkExperience::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBriefcase;

    protected static ?string $navigationLabel = 'panel.nav.work_experience';

    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            LocaleTabs::make([
                ['name' => 'company', 'label' => __('panel.fields.company'), 'required' => true],
                ['name' => 'position', 'label' => __('panel.fields.position'), 'required' => true],
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
                LocaleTextColumn::make('company')->label(__('panel.fields.company')),
                LocaleTextColumn::make('position')->label(__('panel.fields.position')),
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
            'index' => ManageWorkExperiences::route('/'),
        ];
    }
}
