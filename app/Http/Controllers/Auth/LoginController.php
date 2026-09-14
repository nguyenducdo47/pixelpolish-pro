<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Support\Studio;
use App\Enums\ContentProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class LoginController extends Controller
{
    public function create(): Response
    {
        $profile = ContentProfile::tryFrom((string) request('profile', ''));

        return Inertia::render('Auth/Login', [
            'registerUrl' => $profile
                ? '/register?profile='.$profile->value
                : '/register',
        ]);
    }

    public function store(LoginRequest $request): SymfonyResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        $home = $request->user()?->isAdmin() ? '/admin' : Studio::home();

        return Inertia::location(redirect()->intended($home));
    }

    public function destroy(Request $request): SymfonyResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Inertia::location(redirect('/'));
    }
}
