<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Support\Studio;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class ImpersonationController extends Controller
{
    public function enter(User $user): RedirectResponse
    {
        abort_unless(auth()->user()?->isAdmin() === true, 403);
        abort_if(session()->has('impersonator_id'), 403);

        abort_if($user->isDisabled() || $user->trashed(), 403);

        if ($user->is(auth()->user())) {
            return redirect(Studio::home());
        }

        $adminId = auth()->id();
        Auth::login($user);
        session()->put('impersonator_id', $adminId);

        return redirect(Studio::home());
    }

    public function leave(): RedirectResponse
    {
        $adminId = session('impersonator_id');

        abort_unless($adminId, 403);

        Auth::loginUsingId($adminId);
        session()->forget('impersonator_id');

        return redirect('/admin/users');
    }
}
