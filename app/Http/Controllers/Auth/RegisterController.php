<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use App\Models\UserMailPreference;
use App\Support\ContentProfileConfig;
use App\Support\Studio;
use App\Enums\ContentProfile;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class RegisterController extends Controller
{
    public function create(): Response
    {
        $fromQuery = ContentProfile::tryFrom((string) request('profile', ''));

        return Inertia::render('Auth/Register', [
            'initialProfile' => $fromQuery?->value,
            'registerProfile' => $fromQuery ? [
                'key' => $fromQuery->value,
                'label' => $fromQuery->label(),
            ] : null,
        ]);
    }

    public function store(RegisterRequest $request): SymfonyResponse
    {
        $user = User::query()->create($request->safe()->only([
            'name',
            'username',
            'email',
            'password',
        ]));

        $profile = ContentProfile::tryFrom((string) $request->input('content_profile', ''));

        if ($profile && $user->portfolio) {
            $user->portfolio->update(['content_profile' => $profile]);
            $config = ContentProfileConfig::forProfile($profile);
            $config->applySuggestedThemeIfUnset($user->portfolio->fresh());
            $config->syncCvSettingsForSections($user->portfolio->fresh());
        }

        if ($request->boolean('marketing_opt_in')) {
            UserMailPreference::forUser($user)->optInMarketing();
        }

        Auth::login($user);
        $request->session()->regenerate();

        if ($profile) {
            $request->session()->flash('content_profile_welcome', $profile->label());
        }

        return Inertia::location(redirect(Studio::home($profile)));
    }
}
