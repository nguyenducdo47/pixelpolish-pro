<?php

namespace App\Enums;

enum MailCampaignStatus: string
{
    case Draft = 'draft';
    case Scheduled = 'scheduled';
    case Sending = 'sending';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
    case Failed = 'failed';

    public function label(): string
    {
        return __('panel.mail_campaigns.statuses.'.$this->value);
    }
}
