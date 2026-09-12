<?php

namespace App\Filament\Resources\Projects;

use App\Filament\Concerns\TranslatesNavigation;
use App\Filament\Forms\LocaleTabs;
use App\Filament\Resources\Projects\Pages\ManageProjects;
use App\Models\Project;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProjectResource extends Resource
{
    use TranslatesNavigation;

    protected static ?string $model = Project::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBriefcase;

    protected static ?string $navigationLabel = 'panel.nav.projects';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                LocaleTabs::make([
                    ['name' => 'title', 'label' => __('panel.fields.title'), 'required' => true],
                    ['name' => 'subtitle', 'label' => __('panel.fields.subtitle')],
                    ['name' => 'complexity', 'label' => __('panel.fields.complexity')],
                    ['name' => 'summary', 'label' => __('panel.fields.summary'), 'type' => 'editor'],
                    ['name' => 'problem', 'label' => __('panel.fields.problem'), 'type' => 'editor'],
                    ['name' => 'solution', 'label' => __('panel.fields.solution'), 'type' => 'editor'],
                    ['name' => 'learned', 'label' => __('panel.fields.learned'), 'type' => 'editor'],
                    ['name' => 'highlights', 'label' => __('panel.fields.highlights'), 'type' => 'tags'],
                ]),
                TextInput::make('period')->label(__('panel.fields.period')),
                TextInput::make('demo_url')->label(__('panel.fields.demo_url'))->url(),
                TextInput::make('demo_label')->label(__('panel.fields.demo_label')),
                TextInput::make('github_url')->label(__('panel.fields.github_url'))->url(),
                TagsInput::make('tech_stack')->label(__('panel.fields.tech_stack')),
                Toggle::make('is_featured')->label(__('panel.fields.featured'))->default(true),
                TextInput::make('sort_order')->label(__('panel.fields.sort_order'))->numeric()->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->label(__('panel.fields.title'))->formatStateUsing(fn (Project $record) => $record->localeText('title')),
                TextColumn::make('period')->label(__('panel.fields.period')),
                IconColumn::make('is_featured')->label(__('panel.fields.featured'))->boolean(),
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

    public static function getPages(): array
    {
        return [
            'index' => ManageProjects::route('/'),
        ];
    }
}
