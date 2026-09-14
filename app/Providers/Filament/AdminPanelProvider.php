<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Dashboard;
use App\Filament\Pages\ManageMail;
use App\Filament\Resources\UserInboxMessages\UserInboxMessageResource;
use App\Http\Middleware\ApplyUiLocale;
use App\Http\Middleware\AuthenticateAdmin;
use App\Http\Middleware\EnsureUserIsActive;
use Filament\Actions\Action;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Filament\View\PanelsRenderHook;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->profile()
            ->brandName('Portfotilo')
            ->colors([
                'primary' => Color::hex('#1ea1ad'),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
                ManageMail::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                AccountWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                ApplyUiLocale::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                EnsureUserIsActive::class,
                AuthenticateAdmin::class,
            ])
            ->userMenuItems([
                'inbox' => fn (): Action => Action::make('inbox')
                    ->label(fn (): string => __('panel.inbox.title'))
                    ->icon(Heroicon::OutlinedBell)
                    ->url(fn (): string => UserInboxMessageResource::getUrl('index'))
                    ->badge(fn (): ?string => ($count = auth()->user()?->unreadInboxCount() ?? 0) > 0
                        ? (string) ($count > 99 ? '99+' : $count)
                        : null)
                    ->badgeColor('danger')
                    ->sort(-10),
                'logout' => fn (Action $action): Action => $action->label(fn (): string => __('panel.nav.logout')),
            ])
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn () => view('partials.ui-locale-script')
            )
            ->renderHook(
                PanelsRenderHook::GLOBAL_SEARCH_BEFORE,
                fn () => view('filament.clear-cache')
            )
            ->renderHook(
                PanelsRenderHook::USER_MENU_BEFORE,
                fn () => view('filament.locale-switcher')
            )
            ->renderHook(
                PanelsRenderHook::USER_MENU_BEFORE,
                fn () => view('filament.inbox-bell')
            )
            ->renderHook(
                PanelsRenderHook::BODY_START,
                fn () => session()->has('impersonator_id')
                    ? view('filament.impersonation-banner')
                    : ''
            );
    }
}
