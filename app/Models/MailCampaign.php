<?php

namespace App\Models;

use App\Enums\MailCampaignRecipientStatus;
use App\Enums\MailCampaignStatus;
use App\Enums\MailTemplateKind;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MailCampaign extends Model
{
    protected $fillable = [
        'mail_template_id',
        'name',
        'kind',
        'audience',
        'send_at',
        'timezone',
        'status',
        'recipients_total',
        'sent_count',
        'failed_count',
        'skipped_count',
        'started_at',
        'completed_at',
        'cancelled_at',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'audience' => 'array',
            'send_at' => 'datetime',
            'status' => MailCampaignStatus::class,
            'kind' => MailTemplateKind::class,
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(MailTemplate::class, 'mail_template_id');
    }

    public function recipients(): HasMany
    {
        return $this->hasMany(MailCampaignRecipient::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isEditable(): bool
    {
        return in_array($this->status, [MailCampaignStatus::Draft, MailCampaignStatus::Scheduled], true);
    }

    public function refreshStats(): void
    {
        $this->update([
            'sent_count' => $this->recipients()->where('status', MailCampaignRecipientStatus::Sent)->count(),
            'failed_count' => $this->recipients()->where('status', MailCampaignRecipientStatus::Failed)->count(),
            'skipped_count' => $this->recipients()->where('status', MailCampaignRecipientStatus::Skipped)->count(),
        ]);
    }
}
