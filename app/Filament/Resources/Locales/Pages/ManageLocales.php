<?php

namespace App\Filament\Resources\Locales\Pages;

use App\Filament\Resources\Locales\LocaleResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageLocales extends ManageRecords
{
    protected static string $resource = LocaleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
