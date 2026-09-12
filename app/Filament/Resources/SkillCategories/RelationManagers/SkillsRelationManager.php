<?php

namespace App\Filament\Resources\SkillCategories\RelationManagers;

use App\Filament\Forms\LocaleTabs;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class SkillsRelationManager extends RelationManager
{
    protected static string $relationship = 'skills';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('panel.nav.skill_items');
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->label(__('panel.fields.name'))->required()->maxLength(255),
            TextInput::make('level')->label(__('panel.fields.level'))->numeric()->minValue(0)->maxValue(100)->default(50),
            TextInput::make('sort_order')->label(__('panel.fields.sort_order'))->numeric()->default(0),
            LocaleTabs::make([
                ['name' => 'description', 'label' => __('panel.fields.description'), 'type' => 'editor'],
            ]),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')->label(__('panel.fields.name'))->searchable(),
                TextColumn::make('level')->label(__('panel.fields.level')),
                TextColumn::make('sort_order')->label(__('panel.fields.sort_order')),
            ])
            ->headerActions([
                CreateAction::make(),
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
}
