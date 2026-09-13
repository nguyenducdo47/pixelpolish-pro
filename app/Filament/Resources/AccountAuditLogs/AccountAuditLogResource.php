<?php

namespace App\Filament\Resources\AccountAuditLogs;

use App\Enums\AccountAuditAction;
use App\Filament\Concerns\ConfiguresAdminTables;
use App\Filament\Concerns\TranslatesNavigation;
use App\Filament\Resources\AccountAuditLogs\Pages\ManageAccountAuditLogs;
use App\Models\AccountAuditLog;
use App\Models\User;
use BackedEnum;
use Filament\Actions\ViewAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class AccountAuditLogResource extends Resource
{
    use ConfiguresAdminTables;
    use TranslatesNavigation;

    protected static ?string $model = AccountAuditLog::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static ?string $navigationLabel = 'panel.nav.account_audit_logs';

    protected static string|UnitEnum|null $navigationGroup = 'panel.nav.settings';

    protected static ?int $navigationSort = 91;

    protected static ?string $recordTitleAttribute = 'id';

    public static function canViewAny(): bool
    {
        return auth()->user()?->isAdmin() === true
            && ! session()->has('impersonator_id')
            && Filament::getCurrentPanel()?->getId() === 'admin';
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return static::configureAdminListingTable($table)
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('created_at')
                    ->label(__('panel.fields.logged_at'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('action')
                    ->label(__('panel.fields.audit_action'))
                    ->badge()
                    ->formatStateUsing(fn ($state) => __('panel.audit.actions.'.$state->value))
                    ->toggleable(),
                TextColumn::make('subject.name')
                    ->label(__('panel.fields.account_subject'))
                    ->searchable()
                    ->placeholder('—'),
                TextColumn::make('subject.email')
                    ->label(__('panel.fields.email'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('subject.username')
                    ->label(__('panel.fields.username'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('actor.name')
                    ->label(__('panel.fields.account_actor'))
                    ->placeholder(__('panel.audit.system_actor'))
                    ->toggleable(),
                TextColumn::make('actor.email')
                    ->label(__('panel.fields.email'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('reason')
                    ->label(__('panel.fields.lock_reason'))
                    ->limit(40)
                    ->tooltip(fn (?string $state): ?string => $state)
                    ->toggleable(),
                TextColumn::make('ip_address')
                    ->label(__('panel.fields.ip_address'))
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('action')
                    ->label(__('panel.fields.audit_action'))
                    ->options(collect(AccountAuditAction::cases())
                        ->mapWithKeys(fn ($case) => [$case->value => __('panel.audit.actions.'.$case->value)])
                        ->all()),
                SelectFilter::make('subject_user_id')
                    ->label(__('panel.fields.account_subject'))
                    ->searchable()
                    ->options(fn (): array => User::query()
                        ->withTrashed()
                        ->orderBy('name')
                        ->pluck('name', 'id')
                        ->all()),
                SelectFilter::make('actor_user_id')
                    ->label(__('panel.fields.account_actor'))
                    ->searchable()
                    ->options(fn (): array => User::query()
                        ->orderBy('name')
                        ->pluck('name', 'id')
                        ->all()),
                Filter::make('logged_between')
                    ->label(__('panel.fields.logged_at'))
                    ->form([
                        DatePicker::make('from')->label(__('panel.filters.from')),
                        DatePicker::make('until')->label(__('panel.filters.until')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'] ?? null,
                                fn (Builder $inner, string $date): Builder => $inner->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['until'] ?? null,
                                fn (Builder $inner, string $date): Builder => $inner->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->recordActions([
                ViewAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageAccountAuditLogs::route('/'),
        ];
    }
}
