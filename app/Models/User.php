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
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'username', 'email', 'password', 'is_admin', 'is_disabled', 'lock_reason', 'disabled_at'])]
#[Hidden(['password', 'remember_token'])]
#[ObservedBy(UserObserver::class)]
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'is_disabled' => 'boolean',
            'disabled_at' => 'datetime',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->is_admin === true;
    }

    public function isDisabled(): bool
    {
        return $this->is_disabled === true;
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if ($this->isDisabled() || $this->trashed()) {
            return false;
        }

        if ($panel->getId() === 'admin') {
            return $this->isAdmin() && ! session()->has('impersonator_id');
        }

        return true;
    }

    public function portfolio(): HasOne
    {
        return $this->hasOne(Portfolio::class);
    }

    public function accountAuditLogsAsSubject(): HasMany
    {
        return $this->hasMany(AccountAuditLog::class, 'subject_user_id');
    }

    public function sendPasswordResetNotification(#[\SensitiveParameter] $token): void
    {
        $this->notify(
            (new ResetPasswordNotification($token))->locale(UiLocale::current())
        );
    }
}
