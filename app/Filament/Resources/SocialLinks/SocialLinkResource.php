<?php

namespace App\Filament\Resources\SocialLinks;

use App\Filament\Concerns\TranslatesNavigation;
use App\Filament\Resources\SocialLinks\Pages\ManageSocialLinks;
use App\Models\SocialLink;
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

class SocialLinkResource extends Resource
{
    use TranslatesNavigation;

    protected static ?string $model = SocialLink::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShare;

    protected static ?string $navigationLabel = 'panel.nav.social';

    protected static ?int $navigationSort = 7;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('platform')
                ->label(__('panel.fields.platform'))
                ->options([
                    'github' => 'GitHub',
                    'linkedin' => 'LinkedIn',
                    'facebook' => 'Facebook',
                    'twitter' => 'Twitter / X',
                    'instagram' => 'Instagram',
                    'telegram' => 'Telegram',
                    'zalo' => 'Zalo',
                    'website' => 'Website',
                    'other' => __('panel.fields.other'),
                ])
                ->native(false)
                ->required(),
            TextInput::make('url')->label(__('panel.fields.url'))->url()->required(),
            TextInput::make('sort_order')->label(__('panel.fields.sort_order'))->numeric()->default(0),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('platform')->label(__('panel.fields.platform'))->badge(),
                TextColumn::make('url')->label(__('panel.fields.url'))->limit(40),
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
            'index' => ManageSocialLinks::route('/'),
        ];
    }
}
