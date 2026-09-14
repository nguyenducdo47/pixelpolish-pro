<?php

namespace App\Filament\Resources\SkillCategories;

use App\Filament\Concerns\RespectsContentProfileSection;
use App\Filament\Concerns\TranslatesNavigation;
use App\Filament\Forms\LocaleTabs;
use App\Filament\Resources\SkillCategories\Pages\ManageSkillCategories;
use App\Filament\Resources\SkillCategories\RelationManagers\SkillsRelationManager;
use App\Filament\Tables\Columns\LocaleTextColumn;
use App\Models\SkillCategory;
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

class SkillCategoryResource extends Resource
{
    use RespectsContentProfileSection;
    use TranslatesNavigation;

    protected static function contentProfileSectionKey(): ?string
    {
        return 'skills';
    }

    protected static ?string $model = SkillCategory::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSparkles;

    protected static ?string $navigationLabel = 'panel.nav.skills';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            LocaleTabs::make([
                ['name' => 'name', 'label' => __('panel.fields.category_name'), 'required' => true],
            ]),
            TextInput::make('sort_order')->label(__('panel.fields.sort_order'))->numeric()->default(0),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                LocaleTextColumn::make('name')->label(__('panel.fields.name')),
                TextColumn::make('skills_count')->counts('skills')->label(__('panel.fields.skills')),
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
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            SkillsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageSkillCategories::route('/'),
        ];
    }
}
