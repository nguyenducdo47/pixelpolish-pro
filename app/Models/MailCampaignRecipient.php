<?php

namespace App\Models;

use App\Enums\MailCampaignRecipientStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MailCampaignRecipient extends Model
{
    protected $fillable = [
        'mail_campaign_id',
        'user_id',
        'email',
        'status',
        'attempts',
        'error_message',
        'sent_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => MailCampaignRecipientStatus::class,
            'sent_at' => 'datetime',
        ];
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(MailCampaign::class, 'mail_campaign_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
