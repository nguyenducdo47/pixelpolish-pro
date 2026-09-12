<?php

namespace App\Http\Controllers;

use Filament\Notifications\Notification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Artisan;
use Throwable;

class ClearCacheController extends Controller
{
    public function __invoke(): RedirectResponse
    {
        Artisan::call('optimize:clear');

        try {
            Artisan::call('filament:clear-cached-components');
        } catch (Throwable) {
        }

        Notification::make()
            ->title(__('panel.notify.cache_cleared'))
            ->success()
            ->send();

        return back();
    }
}
