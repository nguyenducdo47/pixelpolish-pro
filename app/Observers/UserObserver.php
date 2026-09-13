<?php

namespace App\Observers;

use App\Enums\AccountAuditAction;
use App\Models\User;
use App\Services\AccountAuditLogger;
use App\Support\LocaleCatalog;
use Illuminate\Support\Str;

class UserObserver
{
    public function creating(User $user): void
    {
        if (filled($user->username)) {
            return;
        }

        $base = Str::slug($user->name) ?: Str::before($user->email, '@');
        $username = $base;
        $i = 1;

        while (User::query()->where('username', $username)->exists()) {
            $username = $base.'-'.$i++;
        }

        $user->username = $username;
    }

    public function created(User $user): void
    {
        if ($user->portfolio()->exists()) {
            return;
        }

        $empty = LocaleCatalog::emptyStrings();

        $portfolio = $user->portfolio()->create([
            'slug' => $user->username,
            'is_published' => false,
            'default_locale' => LocaleCatalog::defaultCode(),
            'default_theme' => 'system',
        ]);

        $portfolio->profile()->create([
            'full_name' => $user->name,
            'email' => $user->email,
            'headline' => $empty,
            'tagline' => $empty,
            'about' => $empty,
            'philosophy_quote' => $empty,
        ]);

        $portfolio->cvSettings()->create();

        AccountAuditLogger::log($user, AccountAuditAction::Registered);
    }

    public function updated(User $user): void
    {
        if ($user->wasChanged('username') && $user->portfolio) {
            $user->portfolio->update(['slug' => $user->username]);
        }
    }
}
