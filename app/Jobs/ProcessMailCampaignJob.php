<?php

namespace App\Jobs;

use App\Enums\MailCampaignRecipientStatus;
use App\Enums\MailCampaignStatus;
use App\Models\MailCampaign;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessMailCampaignJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $campaignId,
    ) {}

    public function handle(): void
    {
        $campaign = MailCampaign::query()->find($this->campaignId);

        if (! $campaign || $campaign->status === MailCampaignStatus::Cancelled) {
            return;
        }

        if (! in_array($campaign->status, [MailCampaignStatus::Scheduled, MailCampaignStatus::Sending], true)) {
            return;
        }

        if ($campaign->send_at && $campaign->send_at->isFuture()) {
            return;
        }

        if ($campaign->status === MailCampaignStatus::Scheduled) {
            $campaign->update([
                'status' => MailCampaignStatus::Sending,
                'started_at' => $campaign->started_at ?? now(),
            ]);
        }

        $delaySeconds = 0;

        $campaign->recipients()
            ->where('status', MailCampaignRecipientStatus::Pending)
            ->orderBy('id')
            ->chunkById(50, function ($recipients) use (&$delaySeconds): void {
                foreach ($recipients as $recipient) {
                    SendCampaignRecipientJob::dispatch($recipient->id)
                        ->delay(now()->addSeconds($delaySeconds));

                    $delaySeconds += 2;
                }
            });
    }
}
