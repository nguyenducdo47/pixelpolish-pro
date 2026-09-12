<?php

namespace App\Models;

use App\Notifications\ResetPasswordNotification;
use App\Observers\UserObserver;
use App\Support\UiLocale;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'username', 'email', 'password', 'is_admin'])]
#[Hidden(['password', 'remember_token'])]
#[ObservedBy(UserObserver::class)]
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->is_admin === true;
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'admin') {
            return $this->isAdmin() && ! session()->has('impersonator_id');
        }

        return true;
    }

    public function portfolio(): HasOne
    {
        return $this->hasOne(Portfolio::class);
    }

    public function sendPasswordResetNotification(#[\SensitiveParameter] $token): void
    {
        $this->notify(
            (new ResetPasswordNotification($token))->locale(UiLocale::current())
        );
    }
}
