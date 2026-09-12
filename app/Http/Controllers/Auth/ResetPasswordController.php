<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Support\Studio;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class ResetPasswordController extends Controller
{
    public function create(Request $request, string $token): Response
    {
        return Inertia::render('Auth/ResetPassword', [
            'token' => $token,
            'email' => $request->string('email')->toString(),
        ]);
    }

    public function store(ResetPasswordRequest $request): SymfonyResponse
    {
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request): void {
                $user->forceFill([
                    'password' => $request->string('password')->toString(),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));

                Auth::login($user);
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            throw ValidationException::withMessages([
                'email' => __($status),
            ]);
        }

        $request->session()->regenerate();

        $home = $request->user()?->isAdmin() ? '/admin' : Studio::home();

        return Inertia::location(redirect()->intended($home));
    }
}
