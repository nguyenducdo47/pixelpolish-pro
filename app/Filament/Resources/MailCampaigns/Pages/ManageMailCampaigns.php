<?php

namespace App\Filament\Resources\MailCampaigns\Pages;

use App\Filament\Resources\MailCampaigns\MailCampaignResource;
use App\Models\MailTemplate;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Contracts\Support\Htmlable;

class ManageMailCampaigns extends ManageRecords
{
    protected static string $resource = MailCampaignResource::class;

    public function getTitle(): string|Htmlable
    {
        return __('panel.mail_campaigns.pages.campaigns');
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->mutateFormDataUsing(function (array $data): array {
                    $template = MailTemplate::query()->find($data['mail_template_id'] ?? null);

                    $data = MailCampaignResource::mergeAudienceFormData(
                        $data,
                        $template?->kind,
                    );

                    if ($template) {
                        $data['kind'] = $template->kind;
                    }

                    $data['created_by'] = auth()->id();
                    $data['status'] = \App\Enums\MailCampaignStatus::Draft;

                    return $data;
                }),
        ];
    }
}
