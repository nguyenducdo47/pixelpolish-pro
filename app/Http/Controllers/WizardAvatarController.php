<?php

namespace App\Http\Controllers;

use App\Support\WizardPendingAvatar;
use Symfony\Component\HttpFoundation\StreamedResponse;

class WizardAvatarController extends Controller
{
    public function show(): StreamedResponse
    {
        $portfolio = auth()->user()?->portfolio;

        abort_unless($portfolio !== null, 404);

        return WizardPendingAvatar::response($portfolio->id);
    }
}
