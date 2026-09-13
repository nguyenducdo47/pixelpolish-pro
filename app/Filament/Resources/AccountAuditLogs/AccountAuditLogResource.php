<?php

namespace App\Filament\Resources\AccountAuditLogs;

use App\Filament\Concerns\TranslatesNavigation;
use App\Filament\Resources\AccountAuditLogs\Pages\ManageAccountAuditLogs;
use App\Models\AccountAuditLog;
use App\Models\User;
use BackedEnum;
use Filament\Actions\ViewAction;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

class AccountAuditLogResource extends Resource
{
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
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('created_at')
                    ->label(__('panel.fields.logged_at'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('action')
                    ->label(__('panel.fields.audit_action'))
                    ->badge()
                    ->formatStateUsing(fn ($state) => __('panel.audit.actions.'.$state->value)),
                TextColumn::make('subject.name')
                    ->label(__('panel.fields.account_subject'))
                    ->searchable()
                    ->placeholder('—'),
                TextColumn::make('subject.email')
                    ->label(__('panel.fields.email'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('actor.name')
                    ->label(__('panel.fields.account_actor'))
                    ->placeholder(__('panel.audit.system_actor')),
                TextColumn::make('reason')
                    ->label(__('panel.fields.lock_reason'))
                    ->limit(40)
                    ->tooltip(fn (?string $state): ?string => $state),
                TextColumn::make('ip_address')
                    ->label(__('panel.fields.ip_address'))
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('action')
                    ->label(__('panel.fields.audit_action'))
                    ->options(collect(\App\Enums\AccountAuditAction::cases())
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
