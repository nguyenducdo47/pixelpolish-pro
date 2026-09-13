<?php

namespace App\Http\Middleware;

use Filament\Facades\Filament;
use Filament\Http\Middleware\Authenticate as FilamentAuthenticate;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Http\Exceptions\HttpResponseException;

class AuthenticateStudio extends FilamentAuthenticate
{
    protected function authenticate($request, array $guards): void
    {
        $guard = Filament::auth();

        if (! $guard->check()) {
            $this->unauthenticated($request, $guards);

            return;
        }

        $this->auth->shouldUse(Filament::getAuthGuard());

        /** @var \Illuminate\Database\Eloquent\Model $user */
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

        abort_if(
            $user instanceof FilamentUser ?
                (! $user->canAccessPanel($panel)) :
                (config('app.env') !== 'local'),
            403,
        );
    }

    protected function redirectTo($request): ?string
    {
        return route('login');
    }
}
