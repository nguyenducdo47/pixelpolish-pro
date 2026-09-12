<?php

namespace App\Filament\Resources\TranslationApis\Pages;

use App\Filament\Resources\TranslationApis\TranslationApiResource;
use App\Models\TranslationApi;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;

class ManageTranslationApis extends ManageRecords
{
    protected static string $resource = TranslationApiResource::class;

    public function mount(): void
    {
        parent::mount();

        if (TranslationApi::stored()->isEmpty()) {
            TranslationApi::restoreDefaults();
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('restoreDefaults')
                ->label(__('panel.actions.restore_defaults'))
                ->color('gray')
                ->action(function (): void {
                    $created = TranslationApi::restoreDefaults();

                    Notification::make()
                        ->title(__('panel.notify.translation_apis_restored'))
                        ->body($created > 0
                            ? __('panel.notify.translation_apis_restored_count', ['count' => $created])
                            : __('panel.notify.translation_apis_already_present'))
                        ->success()
                        ->send();
                }),
            CreateAction::make(),
        ];
    }
}
