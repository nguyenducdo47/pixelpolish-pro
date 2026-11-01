<?php

namespace App\Observers;

use App\Enums\AccountAuditAction;
use App\Models\User;
use App\Enums\ContentProfile;
use App\Services\AccountAuditLogger;
use App\Support\LocaleCatalog;
use App\Support\ContentProfileConfig;
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
            'content_profile' => ContentProfile::General,
        ]);

        $portfolio->profile()->create([
            'full_name' => [
                'vi' => $user->name,
                'en' => $user->name,
            ],
            'email' => $user->email,
            'headline' => $empty,
            'tagline' => $empty,
            'about' => $empty,
            'philosophy_quote' => $empty,
        ]);

        $portfolio->cvs()->create([
            'slug' => 'default',
            'name' => ['vi' => 'CV mặc định', 'en' => 'Default CV'],
            'is_default' => true,
        ]);

        ContentProfileConfig::forProfile(ContentProfile::General)->applySuggestedThemeIfUnset($portfolio);

        AccountAuditLogger::log($user, AccountAuditAction::Registered);
    }

    public function updated(User $user): void
    {
        if ($user->wasChanged('username') && $user->portfolio) {
            $user->portfolio->update(['slug' => $user->username]);
        }
    }
}
