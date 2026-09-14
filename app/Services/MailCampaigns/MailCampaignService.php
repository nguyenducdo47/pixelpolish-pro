<?php

namespace App\Services\MailCampaigns;

use App\Enums\MailCampaignRecipientStatus;
use App\Enums\MailCampaignStatus;
use App\Jobs\ProcessMailCampaignJob;
use App\Mail\CampaignMessageMail;
use App\Models\MailCampaign;
use App\Models\MailCampaignRecipient;
use App\Models\MailTemplate;
use App\Models\User;
use App\Services\UserInboxService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use InvalidArgumentException;

class MailCampaignService
{
    public function __construct(
        private CampaignAudienceResolver $audience,
        private CampaignMailRenderer $renderer,
        private UserInboxService $inbox,
    ) {}

    public function sendTemplateTest(MailTemplate $template, User $admin): void
    {
        $rendered = $this->renderer->render($template, $admin);

        Mail::to($admin->email)->send(new CampaignMessageMail(
            subjectLine: $rendered['subject'],
            bodyHtml: $rendered['html'],
            bodyText: $rendered['text'],
        ));

        $this->inbox->recordCampaignMail(
            user: $admin,
            subject: $rendered['subject'],
            bodyHtml: $rendered['html'],
            bodyText: $rendered['text'],
        );
    }

    /**
     * @param  array<string, mixed>  $audience
     */
    public function createDraft(
        MailTemplate $template,
        string $name,
        array $audience,
        ?\DateTimeInterface $sendAt,
        string $timezone,
        ?int $createdBy,
    ): MailCampaign {
        $audience = array_merge($this->audience->defaults($template->kind), $audience);

        return MailCampaign::query()->create([
            'mail_template_id' => $template->id,
            'name' => $name,
            'kind' => $template->kind,
            'audience' => $audience,
            'send_at' => $sendAt,
            'timezone' => $timezone,
            'status' => MailCampaignStatus::Draft,
            'created_by' => $createdBy,
        ]);
    }

    public function materializeRecipients(MailCampaign $campaign): int
    {
        if (! $campaign->isEditable()) {
            throw new InvalidArgumentException(__('panel.mail_campaigns.errors.not_editable'));
        }

        $audience = $campaign->audience ?? [];

        return DB::transaction(function () use ($campaign, $audience): int {
            $campaign->recipients()->delete();

            $count = 0;

            $this->audience->query($audience)->chunkById(200, function ($users) use ($campaign, &$count): void {
                foreach ($users as $user) {
                    if (! filled($user->email)) {
                        continue;
                    }

                    MailCampaignRecipient::query()->create([
                        'mail_campaign_id' => $campaign->id,
                        'user_id' => $user->id,
                        'email' => $user->email,
                        'status' => MailCampaignRecipientStatus::Pending,
                    ]);

                    $count++;
                }
            });

            $campaign->update([
                'recipients_total' => $count,
                'sent_count' => 0,
                'failed_count' => 0,
                'skipped_count' => 0,
            ]);

            return $count;
        });
    }

    public function schedule(MailCampaign $campaign, ?\DateTimeInterface $sendAt): void
    {
        if ($campaign->recipients_total === 0) {
            $this->materializeRecipients($campaign);
            $campaign->refresh();
        }

        if ($campaign->recipients_total === 0) {
            throw new InvalidArgumentException(__('panel.mail_campaigns.errors.no_recipients'));
        }

        $campaign->update([
            'send_at' => $sendAt,
            'status' => MailCampaignStatus::Scheduled,
            'cancelled_at' => null,
        ]);
    }

    public function sendNow(MailCampaign $campaign): void
    {
        if ($campaign->recipients_total === 0) {
            $this->materializeRecipients($campaign->refresh());
        }

        if ($campaign->recipients_total === 0) {
            throw new InvalidArgumentException(__('panel.mail_campaigns.errors.no_recipients'));
        }

        $campaign->update([
            'send_at' => now(),
            'status' => MailCampaignStatus::Sending,
            'started_at' => now(),
            'cancelled_at' => null,
        ]);

        ProcessMailCampaignJob::dispatch($campaign->id);
    }

    public function cancel(MailCampaign $campaign): void
    {
        if (! in_array($campaign->status, [MailCampaignStatus::Scheduled, MailCampaignStatus::Sending], true)) {
            throw new InvalidArgumentException(__('panel.mail_campaigns.errors.cannot_cancel'));
        }

        $campaign->update([
            'status' => MailCampaignStatus::Cancelled,
            'cancelled_at' => now(),
        ]);

        $campaign->recipients()
            ->where('status', MailCampaignRecipientStatus::Pending)
            ->update(['status' => MailCampaignRecipientStatus::Skipped]);
    }

    public function markCompletedIfDone(MailCampaign $campaign): void
    {
        $pending = $campaign->recipients()->where('status', MailCampaignRecipientStatus::Pending)->exists();

        if ($pending) {
            return;
        }

        $campaign->refreshStats();
        $campaign->update([
            'status' => MailCampaignStatus::Completed,
            'completed_at' => now(),
        ]);
    }
}
