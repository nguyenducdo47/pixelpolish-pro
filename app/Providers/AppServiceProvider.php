<?php

namespace App\Providers;

use App\Http\Controllers\LivewireFileUploadController;
use App\Models\MailSetting;
use App\Support\Studio;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Auth\Middleware\RedirectIfAuthenticated;
use Illuminate\Support\ServiceProvider;
use Livewire\Features\SupportFileUploads\FileUploadController;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(FileUploadController::class, LivewireFileUploadController::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RedirectIfAuthenticated::redirectUsing(function () {
            $user = auth()->user();

            if ($user?->isAdmin() && ! session()->has('impersonator_id')) {
                return '/admin';
            }

            return Studio::home();
        });

        FilamentAsset::register([
            Js::make('rich-content-plugins/font-size', resource_path('js/filament/rich-content-plugins/font-size.js'))
                ->module()
                ->loadedOnRequest(),
        ]);

        $this->app->resolving('mail.manager', function (): void {
            MailSetting::applyToConfig();
        });
    }
}
