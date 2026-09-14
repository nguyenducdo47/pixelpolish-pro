<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserInboxMessage extends Model
{
    protected $fillable = [
        'user_id',
        'mail_campaign_recipient_id',
        'subject',
        'body_html',
        'body_text',
        'read_at',
    ];

    protected function casts(): array
    {
        return [
            'read_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function campaignRecipient(): BelongsTo
    {
        return $this->belongsTo(MailCampaignRecipient::class, 'mail_campaign_recipient_id');
    }

    public function isUnread(): bool
    {
        return $this->read_at === null;
    }

    public function markAsRead(): void
    {
        if ($this->read_at === null) {
            $this->update(['read_at' => now()]);
        }
    }
}
