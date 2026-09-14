<?php

namespace App\Filament\Resources\MailTemplates\Pages;

use App\Filament\Resources\MailTemplates\MailTemplateResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Contracts\Support\Htmlable;

class ManageMailTemplates extends ManageRecords
{
    protected static string $resource = MailTemplateResource::class;

    public function getTitle(): string|Htmlable
    {
        return __('panel.mail_campaigns.pages.templates');
    }

    protected function getHeaderActions(): array
    {
        return [
            MailTemplateResource::configureFormModal(CreateAction::make()),
        ];
    }
}
