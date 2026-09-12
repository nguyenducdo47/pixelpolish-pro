<?php

namespace App\Filament\Resources\Principles\Pages;

use App\Filament\Resources\Principles\PrincipleResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManagePrinciples extends ManageRecords
{
    protected static string $resource = PrincipleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
