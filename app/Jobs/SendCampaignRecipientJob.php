<?php

namespace App\Jobs;

use App\Enums\MailCampaignRecipientStatus;
use App\Enums\MailCampaignStatus;
use App\Enums\MailTemplateKind;
use App\Mail\CampaignMessageMail;
use App\Models\MailCampaign;
use App\Models\MailCampaignRecipient;
use App\Models\UserMailPreference;
use App\Services\MailCampaigns\CampaignMailRenderer;
use App\Services\MailCampaigns\MailCampaignService;
use App\Services\UserInboxService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendCampaignRecipientJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $backoff = 30;

    public function __construct(
        public int $recipientId,
    ) {}

    public function handle(CampaignMailRenderer $renderer, MailCampaignService $campaigns, UserInboxService $inbox): void
    {
        $recipient = MailCampaignRecipient::query()->with(['campaign.template', 'user.portfolio'])->find($this->recipientId);

        if (! $recipient || $recipient->status !== MailCampaignRecipientStatus::Pending) {
            return;
        }

        $campaign = $recipient->campaign;

        if (! $campaign || $campaign->status === MailCampaignStatus::Cancelled) {
            $recipient->update(['status' => MailCampaignRecipientStatus::Skipped]);

            return;
        }

        $user = $recipient->user;

        if (! $user) {
            $recipient->update([
                'status' => MailCampaignRecipientStatus::Skipped,
                'error_message' => 'User missing',
            ]);

            return;
        }

        if ($campaign->kind === MailTemplateKind::Marketing) {
            $pref = UserMailPreference::forUser($user);

            if (! $pref->wantsMarketingEmails()) {
                $recipient->update(['status' => MailCampaignRecipientStatus::Skipped]);
                $campaign->increment('skipped_count');

                return;
            }
        }

        $template = $campaign->template;

        if (! $template) {
            $recipient->update([
                'status' => MailCampaignRecipientStatus::Failed,
                'error_message' => 'Template missing',
            ]);

            return;
        }

        try {
            $rendered = $renderer->render($template, $user);

            Mail::to($recipient->email)->send(new CampaignMessageMail(
                subjectLine: $rendered['subject'],
                bodyHtml: $rendered['html'],
                bodyText: $rendered['text'],
            ));

            $recipient->update([
                'status' => MailCampaignRecipientStatus::Sent,
                'sent_at' => now(),
                'attempts' => $recipient->attempts + 1,
                'error_message' => null,
            ]);

            $inbox->recordCampaignMail(
                user: $user,
                subject: $rendered['subject'],
                bodyHtml: $rendered['html'],
                bodyText: $rendered['text'],
                mailCampaignRecipientId: $recipient->id,
            );

            $campaign->increment('sent_count');
            $campaigns->markCompletedIfDone($campaign->fresh());
        } catch (Throwable $exception) {
            $recipient->update([
                'attempts' => $recipient->attempts + 1,
                'error_message' => $exception->getMessage(),
            ]);

            if ($this->attempts() >= $this->tries) {
                $recipient->update(['status' => MailCampaignRecipientStatus::Failed]);
                $campaign->increment('failed_count');
                $campaigns->markCompletedIfDone($campaign->fresh());
            }

            throw $exception;
        }
    }
}
