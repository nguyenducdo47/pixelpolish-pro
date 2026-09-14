<?php

namespace App\Http\Controllers;

use App\Models\UserMailPreference;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MailUnsubscribeController extends Controller
{
    public function __invoke(Request $request, string $token): Response
    {
        if (! $request->hasValidSignature()) {
            abort(403);
        }

        $preference = UserMailPreference::query()->where('unsubscribe_token', $token)->firstOrFail();

        if (! $preference->isMarketingUnsubscribed()) {
            $preference->unsubscribeMarketing();
        }

        return Inertia::render('Mail/Unsubscribe', [
            'title' => __('mail.unsubscribe.title'),
            'message' => __('mail.unsubscribe.done'),
        ]);
    }
}
