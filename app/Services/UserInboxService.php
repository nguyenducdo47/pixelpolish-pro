<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserInboxMessage;
use Illuminate\Support\Facades\Schema;

class UserInboxService
{
    public function recordCampaignMail(
        User $user,
        string $subject,
        string $bodyHtml,
        ?string $bodyText = null,
        ?int $mailCampaignRecipientId = null,
    ): UserInboxMessage {
        return UserInboxMessage::query()->create([
            'user_id' => $user->id,
            'mail_campaign_recipient_id' => $mailCampaignRecipientId,
            'subject' => $subject,
            'body_html' => $bodyHtml,
            'body_text' => $bodyText,
        ]);
    }

    public function markAllRead(User $user): int
    {
        return UserInboxMessage::query()
            ->where('user_id', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    public function deleteAll(User $user): int
    {
        return UserInboxMessage::query()
            ->where('user_id', $user->id)
            ->delete();
    }

    public function unreadCount(User $user): int
    {
        if (! Schema::hasTable('user_inbox_messages')) {
            return 0;
        }

        return UserInboxMessage::query()
            ->where('user_id', $user->id)
            ->whereNull('read_at')
            ->count();
    }
}
