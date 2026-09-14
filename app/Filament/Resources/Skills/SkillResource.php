<?php

namespace App\Filament\Resources\Skills;

use App\Filament\Concerns\RespectsContentProfileSection;
use App\Filament\Concerns\TranslatesNavigation;
use App\Filament\Forms\LocaleTabs;
use App\Filament\Resources\Skills\Pages\ManageSkills;
use App\Support\ContentProfileConfig;
use App\Filament\Tables\Columns\LocaleTextColumn;
use App\Models\Skill;
use App\Models\SkillCategory;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SkillResource extends Resource
{
    use RespectsContentProfileSection;
    use TranslatesNavigation;

    protected static function contentProfileSectionKey(): ?string
    {
        return 'skills';
    }

    protected static ?string $model = Skill::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedWrenchScrewdriver;

    protected static ?string $navigationLabel = 'panel.nav.skill_items';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('skill_category_id')
                ->label(__('panel.fields.category'))
                ->options(fn () => SkillCategory::query()->get()->mapWithKeys(
                    fn (SkillCategory $category) => [$category->id => $category->localeText('name')]
                ))
                ->native(false)
                ->required(),
            TextInput::make('name')->label(__('panel.fields.name'))->required(),
            TextInput::make('level')
                ->label(__('panel.fields.level'))
                ->numeric()
                ->minValue(0)
                ->maxValue(100)
                ->default(50)
                ->visible(fn (): bool => ContentProfileConfig::for(auth()->user()?->portfolio)->usesSkillPercent()),
            TextInput::make('sort_order')->label(__('panel.fields.sort_order'))->numeric()->default(0),
            LocaleTabs::make([
                ['name' => 'description', 'label' => __('panel.fields.description'), 'type' => 'editor'],
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                LocaleTextColumn::make('category.name')->label(__('panel.fields.category')),
                TextColumn::make('name')->label(__('panel.fields.name'))->searchable(),
                TextColumn::make('level')->label(__('panel.fields.level')),
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
            'index' => ManageSkills::route('/'),
        ];
    }
}
