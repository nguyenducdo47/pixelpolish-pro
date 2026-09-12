<?php

namespace App\Filament\Resources\Users;

use App\Filament\Concerns\TranslatesNavigation;
use App\Filament\Resources\Users\Pages\ManageUsers;
use App\Models\User;
use App\Support\LocaleCatalog;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class UserResource extends Resource
{
    use TranslatesNavigation;

    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static ?string $navigationLabel = 'panel.nav.users';

    protected static string|UnitEnum|null $navigationGroup = 'panel.nav.settings';

    protected static ?int $navigationSort = 90;

    protected static ?string $recordTitleAttribute = 'name';

    public static function canViewAny(): bool
    {
        return auth()->user()?->isAdmin() === true
            && ! session()->has('impersonator_id')
            && Filament::getCurrentPanel()?->getId() === 'admin';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('panel.sections.account'))->schema([
                TextInput::make('name')->label(__('panel.fields.full_name'))->required()->maxLength(255),
                TextInput::make('username')
                    ->label(__('panel.fields.username'))
                    ->required()
                    ->alphaDash()
                    ->unique(ignoreRecord: true)
                    ->helperText(__('panel.fields.username_helper')),
                TextInput::make('email')->label(__('panel.fields.email'))->email()->required()->unique(ignoreRecord: true),
                TextInput::make('password')
                    ->label(__('panel.fields.password'))
                    ->password()
                    ->revealable()
                    ->dehydrated(fn (?string $state) => filled($state))
                    ->required(fn (?User $record): bool => $record === null)
                    ->helperText(fn (?User $record) => $record ? __('panel.fields.password_update_helper') : __('panel.fields.password_create_helper')),
                Toggle::make('is_admin')
                    ->label(__('panel.fields.is_admin'))
                    ->helperText(__('panel.fields.is_admin_helper'))
                    ->disabled(fn (?User $record): bool => $record !== null && static::cannotDemoteAdmin($record)),
            ])->columns(2),
            Section::make(__('panel.sections.portfolio'))->schema([
                Select::make('default_locale')
                    ->label(__('panel.fields.default_locale'))
                    ->options(fn () => LocaleCatalog::options())
                    ->default(fn () => LocaleCatalog::defaultCode())
                    ->required()
                    ->dehydrated(),
                Toggle::make('is_published')
                    ->label(__('panel.fields.publish_now'))
                    ->default(false)
                    ->dehydrated(),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with('portfolio'))
            ->columns([
                TextColumn::make('name')->label(__('panel.fields.full_name'))->searchable(),
                TextColumn::make('username')->label(__('panel.fields.username'))->searchable()->copyable(),
                TextColumn::make('email')->label(__('panel.fields.email'))->searchable(),
                IconColumn::make('is_admin')->boolean()->label(__('panel.fields.admin')),
                IconColumn::make('portfolio.is_published')->boolean()->label(__('panel.fields.published')),
                TextColumn::make('portfolio.default_locale')->label(__('panel.fields.locale'))->badge(),
            ])
            ->recordActions([
                Action::make('impersonate')
                    ->label(__('panel.actions.edit_portfolio'))
                    ->button()
                    ->icon(Heroicon::OutlinedPencilSquare)
                    ->color('warning')
                    ->url(fn (User $record): string => route('impersonation.enter', $record)),
                Action::make('viewPublic')
                    ->label(__('panel.actions.view_site'))
                    ->icon(Heroicon::OutlinedArrowTopRightOnSquare)
                    ->url(function (User $record): string {
                        $locale = $record->portfolio?->default_locale ?: LocaleCatalog::defaultCode();

                        return url('/'.$locale.'/'.$record->username);
                    })
                    ->openUrlInNewTab()
                    ->visible(fn (User $record): bool => filled($record->username)),
                EditAction::make()
                    ->mutateRecordDataUsing(function (array $data, User $record): array {
                        $data['is_published'] = (bool) $record->portfolio?->is_published;
                        $data['default_locale'] = $record->portfolio?->default_locale ?: LocaleCatalog::defaultCode();
                        $data['password'] = null;

                        return $data;
                    })
                    ->using(fn (User $record, array $data): User => static::saveUser($record, $data, isCreate: false)),
                DeleteAction::make()
                    ->disabled(fn (User $record): bool => static::cannotDeleteUser($record)),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function saveUser(?User $record, array $data, bool $isCreate): User
    {
        $isPublished = (bool) ($data['is_published'] ?? false);
        $defaultLocale = $data['default_locale'] ?? LocaleCatalog::defaultCode();
        unset($data['is_published'], $data['default_locale']);

        if (! $isCreate && blank($data['password'] ?? null)) {
            unset($data['password']);
        }

        if (! $isCreate && $record && static::cannotDemoteAdmin($record)) {
            $data['is_admin'] = true;
        }

        if ($isCreate) {
            $record = User::query()->create($data);
        } else {
            $record->update($data);
            $record->refresh();
        }

        $record->portfolio?->update([
            'slug' => $record->username,
            'is_published' => $isPublished,
            'default_locale' => $defaultLocale,
        ]);

        return $record;
    }

    public static function cannotDeleteUser(User $record): bool
    {
        if ($record->getKey() === auth()->id()) {
            return true;
        }

        return static::cannotDemoteAdmin($record);
    }

    public static function cannotDemoteAdmin(User $record): bool
    {
        if (! $record->isAdmin()) {
            return false;
        }

        if ($record->getKey() === auth()->id()) {
            return true;
        }

        return User::query()->where('is_admin', true)->count() <= 1;
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageUsers::route('/'),
        ];
    }
}
