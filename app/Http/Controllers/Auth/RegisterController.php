<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use App\Support\Studio;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class RegisterController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    public function store(RegisterRequest $request): SymfonyResponse
    {
        $user = User::query()->create($request->safe()->only([
            'name',
            'username',
            'email',
            'password',
        ]));

        Auth::login($user);
        $request->session()->regenerate();

        return Inertia::location(redirect(Studio::home()));
    }
}
