<?php

namespace App\Console\Commands;

use App\Enums\MailCampaignStatus;
use App\Jobs\ProcessMailCampaignJob;
use App\Models\MailCampaign;
use Illuminate\Console\Command;

class ProcessScheduledMailCampaigns extends Command
{
    protected $signature = 'mail-campaigns:process-scheduled';

    protected $description = 'Dispatch due scheduled mail campaigns';

    public function handle(): int
    {
        MailCampaign::query()
            ->where('status', MailCampaignStatus::Scheduled)
            ->where(function ($query): void {
                $query->whereNull('send_at')->orWhere('send_at', '<=', now());
            })
            ->orderBy('id')
            ->each(function (MailCampaign $campaign): void {
                $updated = MailCampaign::query()
                    ->whereKey($campaign->id)
                    ->where('status', MailCampaignStatus::Scheduled)
                    ->update([
                        'status' => MailCampaignStatus::Sending,
                        'started_at' => now(),
                    ]);

                if ($updated) {
                    ProcessMailCampaignJob::dispatch($campaign->id);
                }
            });

        return self::SUCCESS;
    }
}
