<?php

namespace App\Filament\Resources\MailCampaigns\Pages;

use App\Enums\MailCampaignStatus;
use App\Filament\Resources\MailCampaigns\MailCampaignResource;
use App\Models\MailTemplate;
use App\Services\MailCampaigns\CampaignAudienceResolver;
use App\Services\MailCampaigns\MailCampaignService;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

class EditMailCampaign extends EditRecord
{
    protected static string $resource = MailCampaignResource::class;

    public function getTitle(): string|Htmlable
    {
        return __('panel.mail_campaigns.pages.edit_campaign', ['name' => $this->record->name]);
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        return MailCampaignResource::expandAudienceFormData($data);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $template = MailTemplate::query()->find($data['mail_template_id'] ?? $this->record->mail_template_id);

        $data = MailCampaignResource::mergeAudienceFormData($data, $template?->kind);

        if ($template) {
            $data['kind'] = $template->kind;
        }

        return $data;
    }

    protected function getHeaderActions(): array
    {
        $record = $this->record;

        return [
            Action::make('estimateAudience')
                ->label(__('panel.mail_campaigns.actions.estimate_audience'))
                ->icon(Heroicon::OutlinedUsers)
                ->action(function (CampaignAudienceResolver $audience): void {
                    $count = $audience->count($this->record->audience ?? []);

                    Notification::make()
                        ->title(__('panel.mail_campaigns.notify.audience_estimate'))
                        ->body(__('panel.mail_campaigns.notify.audience_count', ['count' => $count]))
                        ->success()
                        ->send();
                }),
            Action::make('materialize')
                ->label(__('panel.mail_campaigns.actions.materialize'))
                ->icon(Heroicon::OutlinedUserGroup)
                ->requiresConfirmation()
                ->visible(fn (): bool => $record->isEditable())
                ->action(function (MailCampaignService $service): void {
                    try {
                        $count = $service->materializeRecipients($this->record->fresh());

                        Notification::make()
                            ->title(__('panel.mail_campaigns.notify.materialized'))
                            ->body(__('panel.mail_campaigns.notify.audience_count', ['count' => $count]))
                            ->success()
                            ->send();

                        $this->refreshFormData(['recipients_total', 'sent_count', 'failed_count', 'skipped_count']);
                    } catch (\Throwable $exception) {
                        Notification::make()
                            ->title(__('panel.mail_campaigns.notify.action_failed'))
                            ->body($exception->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
            Action::make('schedule')
                ->label(__('panel.mail_campaigns.actions.schedule'))
                ->icon(Heroicon::OutlinedClock)
                ->visible(fn (): bool => in_array($record->status, [MailCampaignStatus::Draft, MailCampaignStatus::Scheduled], true))
                ->form([
                    DateTimePicker::make('send_at')
                        ->label(__('panel.mail_campaigns.fields.send_at'))
                        ->required()
                        ->seconds(false)
                        ->native(false),
                ])
                ->action(function (array $data, MailCampaignService $service): void {
                    try {
                        $service->schedule($this->record->fresh(), $data['send_at']);

                        Notification::make()
                            ->title(__('panel.mail_campaigns.notify.scheduled'))
                            ->success()
                            ->send();

                        $this->redirect(static::getUrl(['record' => $this->record]));
                    } catch (\Throwable $exception) {
                        Notification::make()
                            ->title(__('panel.mail_campaigns.notify.action_failed'))
                            ->body($exception->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
            Action::make('sendNow')
                ->label(__('panel.mail_campaigns.actions.send_now'))
                ->icon(Heroicon::OutlinedPaperAirplane)
                ->color('success')
                ->requiresConfirmation()
                ->visible(fn (): bool => in_array($record->status, [MailCampaignStatus::Draft, MailCampaignStatus::Scheduled], true))
                ->action(function (MailCampaignService $service): void {
                    try {
                        $service->sendNow($this->record->fresh());

                        Notification::make()
                            ->title(__('panel.mail_campaigns.notify.send_started'))
                            ->success()
                            ->send();

                        $this->redirect(static::getUrl(['record' => $this->record]));
                    } catch (\Throwable $exception) {
                        Notification::make()
                            ->title(__('panel.mail_campaigns.notify.action_failed'))
                            ->body($exception->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
            Action::make('cancel')
                ->label(__('panel.mail_campaigns.actions.cancel'))
                ->icon(Heroicon::OutlinedXCircle)
                ->color('danger')
                ->requiresConfirmation()
                ->visible(fn (): bool => in_array($record->status, [MailCampaignStatus::Scheduled, MailCampaignStatus::Sending], true))
                ->action(function (MailCampaignService $service): void {
                    try {
                        $service->cancel($this->record->fresh());

                        Notification::make()
                            ->title(__('panel.mail_campaigns.notify.cancelled'))
                            ->success()
                            ->send();

                        $this->redirect(static::getUrl(['record' => $this->record]));
                    } catch (\Throwable $exception) {
                        Notification::make()
                            ->title(__('panel.mail_campaigns.notify.action_failed'))
                            ->body($exception->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
            DeleteAction::make()
                ->visible(fn (): bool => $record->status === MailCampaignStatus::Draft),
        ];
    }
}
