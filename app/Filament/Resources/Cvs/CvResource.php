<?php

namespace App\Filament\Resources\Cvs;

use App\Filament\Concerns\TranslatesNavigation;
use App\Filament\Forms\LocaleTabs;
use App\Filament\Resources\Cvs\Pages\CreateCv;
use App\Filament\Resources\Cvs\Pages\EditCv;
use App\Filament\Resources\Cvs\Pages\ListCvs;
use App\Filament\Tables\Columns\LocaleTextColumn;
use App\Models\Cv;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CvResource extends Resource
{
    use TranslatesNavigation;

    protected static ?string $model = Cv::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentDuplicate;

    protected static ?string $navigationLabel = 'panel.nav.cvs';

    protected static ?int $navigationSort = 7;

    protected static ?string $recordTitleAttribute = 'slug';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('slug')
                ->label(__('panel.fields.cv_slug'))
                ->required()
                ->alphaDash()
                ->maxLength(64)
                ->unique(ignoreRecord: true, modifyRuleUsing: fn ($rule) => $rule->where('portfolio_id', auth()->user()?->portfolio?->id)),
            LocaleTabs::make([
                [
                    'name' => 'name',
                    'label' => __('panel.fields.cv_name'),
                    'type' => 'text',
                    'required' => false,
                ],
            ]),
            Toggle::make('is_default')
                ->label(__('panel.fields.cv_is_default'))
                ->helperText(__('panel.fields.cv_is_default_help')),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                LocaleTextColumn::make('name')->label(__('panel.fields.cv_name')),
                TextColumn::make('slug')->label(__('panel.fields.cv_slug')),
                IconColumn::make('is_default')->label(__('panel.fields.cv_is_default'))->boolean(),
            ])
            ->defaultSort('is_default', 'desc')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()
                    ->hidden(fn (Cv $record): bool => $record->is_default),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCvs::route('/'),
            'create' => CreateCv::route('/create'),
            'edit' => EditCv::route('/{record}/edit'),
        ];
    }
}
