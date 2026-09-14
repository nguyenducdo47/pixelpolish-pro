<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class UserMailPreference extends Model
{
    protected $fillable = [
        'user_id',
        'marketing_opted_in_at',
        'marketing_unsubscribed_at',
        'unsubscribe_token',
    ];

    protected function casts(): array
    {
        return [
            'marketing_opted_in_at' => 'datetime',
            'marketing_unsubscribed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function forUser(User $user): self
    {
        return self::query()->firstOrCreate(
            ['user_id' => $user->id],
            ['unsubscribe_token' => Str::random(48)],
        );
    }

    public function wantsMarketingEmails(): bool
    {
        return $this->marketing_opted_in_at !== null
            && $this->marketing_unsubscribed_at === null;
    }

    public function optInMarketing(): void
    {
        $this->update([
            'marketing_opted_in_at' => now(),
            'marketing_unsubscribed_at' => null,
        ]);
    }

    public function optOutMarketing(): void
    {
        $this->update([
            'marketing_opted_in_at' => null,
            'marketing_unsubscribed_at' => now(),
        ]);
    }

    public function isMarketingUnsubscribed(): bool
    {
        return ! $this->wantsMarketingEmails();
    }

    public function unsubscribeMarketing(): void
    {
        $this->update(['marketing_unsubscribed_at' => now()]);
    }
}
