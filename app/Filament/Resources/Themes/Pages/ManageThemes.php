<?php

namespace App\Filament\Resources\Themes\Pages;

use App\Filament\Resources\Themes\ThemeResource;
use App\Models\Theme;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Contracts\Support\Htmlable;

class ManageThemes extends ManageRecords
{
    protected static string $resource = ThemeResource::class;

    public function getTitle(): string|Htmlable
    {
        return __('panel.pages.themes');
    }

    public function mount(): void
    {
        parent::mount();

        if (Theme::query()->doesntExist()) {
            Theme::seedDefaults();
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('restoreDefaults')
                ->label(__('panel.actions.restore_defaults'))
                ->color('gray')
                ->action(function (): void {
                    $created = Theme::seedDefaults();

                    Notification::make()
                        ->title(__('panel.notify.themes_restored'))
                        ->body($created > 0
                            ? __('panel.notify.themes_restored_count', ['count' => $created])
                            : __('panel.notify.themes_already_present'))
                        ->success()
                        ->send();
                }),
            ThemeResource::configureFormModal(CreateAction::make()),
        ];
    }
}
