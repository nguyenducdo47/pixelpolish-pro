<?php

namespace App\Filament\Resources\SpokenLanguages\Pages;

use App\Filament\Resources\SpokenLanguages\SpokenLanguageResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageSpokenLanguages extends ManageRecords
{
    protected static string $resource = SpokenLanguageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
