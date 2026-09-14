<?php

namespace App\Filament\Resources\UserInboxMessages\Pages;

use App\Filament\Resources\UserInboxMessages\UserInboxMessageResource;
use App\Services\UserInboxService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

class ManageUserInboxMessages extends ManageRecords
{
    protected static string $resource = UserInboxMessageResource::class;

    public function getTitle(): string|Htmlable
    {
        return __('panel.inbox.title');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('markAllRead')
                ->label(__('panel.inbox.actions.mark_all_read'))
                ->icon(Heroicon::OutlinedCheckCircle)
                ->action(function (UserInboxService $inbox): void {
                    $user = auth()->user();

                    if (! $user) {
                        return;
                    }

                    $count = $inbox->markAllRead($user);

                    Notification::make()
                        ->title(__('panel.inbox.notify.marked_read'))
                        ->body($count > 0
                            ? __('panel.inbox.notify.marked_read_count', ['count' => $count])
                            : __('panel.inbox.notify.nothing_unread'))
                        ->success()
                        ->send();
                }),
            Action::make('deleteAll')
                ->label(__('panel.inbox.actions.delete_all'))
                ->icon(Heroicon::OutlinedTrash)
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading(__('panel.inbox.actions.delete_all'))
                ->modalDescription(__('panel.inbox.confirm_delete_all'))
                ->action(function (UserInboxService $inbox): void {
                    $user = auth()->user();

                    if (! $user) {
                        return;
                    }

                    $inbox->deleteAll($user);

                    Notification::make()
                        ->title(__('panel.inbox.notify.deleted_all'))
                        ->success()
                        ->send();
                }),
        ];
    }
}
