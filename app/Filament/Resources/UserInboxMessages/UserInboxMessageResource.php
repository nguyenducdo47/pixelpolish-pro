<?php

namespace App\Filament\Resources\UserInboxMessages;

use App\Filament\Concerns\TranslatesNavigation;
use App\Filament\Resources\UserInboxMessages\Pages\ManageUserInboxMessages;
use App\Models\UserInboxMessage;
use App\Services\UserInboxService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\HtmlString;

class UserInboxMessageResource extends Resource
{
    use TranslatesNavigation;

    protected static ?string $model = UserInboxMessage::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBell;

    protected static ?string $navigationLabel = 'panel.nav.inbox';

    protected static ?int $navigationSort = -1;

    protected static bool $shouldRegisterNavigation = true;

    protected static bool $shouldCheckPolicyExistence = false;

    protected static ?string $modelLabel = 'panel.inbox.message';

    protected static ?string $pluralModelLabel = 'panel.inbox.messages';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', auth()->id())
            ->latest('created_at');
    }

    public static function getNavigationBadge(): ?string
    {
        $user = auth()->user();

        if (! $user) {
            return null;
        }

        $count = $user->unreadInboxCount();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'danger';
    }

    public static function canViewAny(): bool
    {
        return auth()->check();
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('subject')
                    ->label(__('panel.inbox.fields.subject'))
                    ->searchable()
                    ->weight(fn (UserInboxMessage $record): string => $record->isUnread() ? 'bold' : 'normal'),
                TextColumn::make('created_at')
                    ->label(__('panel.inbox.fields.received_at'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('read_at')
                    ->label(__('panel.inbox.fields.read_at'))
                    ->dateTime()
                    ->placeholder(__('panel.inbox.unread'))
                    ->toggleable(),
            ])
            ->recordActions([
                ViewAction::make()
                    ->modalHeading(fn (UserInboxMessage $record): string => $record->subject)
                    ->modalContent(fn (UserInboxMessage $record): HtmlString => new HtmlString(
                        '<div class="prose dark:prose-invert max-w-none">'.$record->body_html.'</div>'
                    ))
                    ->after(fn (UserInboxMessage $record) => $record->markAsRead()),
                Action::make('markRead')
                    ->label(__('panel.inbox.actions.mark_read'))
                    ->icon(Heroicon::OutlinedCheck)
                    ->visible(fn (UserInboxMessage $record): bool => $record->isUnread())
                    ->action(fn (UserInboxMessage $record) => $record->markAsRead()),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('markAsRead')
                        ->label(__('panel.inbox.actions.mark_read_selected'))
                        ->icon(Heroicon::OutlinedCheck)
                        ->action(function (Collection $records): void {
                            $records->each(fn (UserInboxMessage $record) => $record->markAsRead());
                        })
                        ->deselectRecordsAfterCompletion(),
                    DeleteBulkAction::make()
                        ->label(__('panel.inbox.actions.delete_selected')),
                ]),
            ])
            ->emptyStateHeading(__('panel.inbox.empty'));
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageUserInboxMessages::route('/'),
        ];
    }
}
