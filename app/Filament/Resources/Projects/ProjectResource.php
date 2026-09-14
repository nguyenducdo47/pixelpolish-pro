<?php

namespace App\Filament\Resources\Projects;

use App\Filament\Concerns\RespectsContentProfileSection;
use App\Filament\Concerns\TranslatesNavigation;
use App\Filament\Forms\LocaleTabs;
use App\Filament\Resources\Projects\Pages\ManageProjects;
use App\Filament\Support\ProjectFormSchema;
use App\Filament\Tables\Columns\LocaleTextColumn;
use App\Models\Project;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
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
    use RespectsContentProfileSection;
    use TranslatesNavigation;

    protected static function contentProfileSectionKey(): ?string
    {
        return 'projects';
    }

    protected static ?string $model = Project::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBriefcase;

    protected static ?string $navigationLabel = 'panel.nav.projects';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Schema $schema): Schema
    {
        $profile = fn (): mixed => auth()->user()?->portfolio?->content_profile;

        return $schema
            ->components([
                LocaleTabs::make(ProjectFormSchema::localeTabFieldDefinitions($profile)),
                ...ProjectFormSchema::extraFields($profile),
                Toggle::make('is_featured')->label(__('panel.fields.featured'))->default(true),
                TextInput::make('sort_order')->label(__('panel.fields.sort_order'))->numeric()->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                LocaleTextColumn::make('title')->label(__('panel.fields.title')),
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
