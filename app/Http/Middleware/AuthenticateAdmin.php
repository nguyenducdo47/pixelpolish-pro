<?php

namespace App\Http\Middleware;

use App\Support\Studio;
use Filament\Facades\Filament;
use Filament\Http\Middleware\Authenticate as FilamentAuthenticate;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Http\Exceptions\HttpResponseException;

class AuthenticateAdmin extends FilamentAuthenticate
{
    protected function authenticate($request, array $guards): void
    {
        $guard = Filament::auth();

        if (! $guard->check()) {
            $this->unauthenticated($request, $guards);

            return;
        }

        $this->auth->shouldUse(Filament::getAuthGuard());

        $user = $guard->user();

        if (method_exists($user, 'isDisabled') && ($user->isDisabled() || $user->trashed())) {
            $guard->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            throw new HttpResponseException(
                redirect()->route('login')->with('status', __('auth.account_disabled'))
            );
        }

        $panel = Filament::getCurrentOrDefaultPanel();

        if ($user instanceof FilamentUser && ! $user->canAccessPanel($panel)) {
            throw new HttpResponseException(redirect(Studio::home()));
        }
    }

    protected function redirectTo($request): ?string
    {
        return route('login');
    }
}
