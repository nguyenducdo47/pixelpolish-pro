<?php

namespace App\Filament\Resources\Cvs\Pages;

use App\Filament\Resources\Cvs\CvResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCv extends CreateRecord
{
    protected static string $resource = CvResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['portfolio_id'] = auth()->user()->portfolio->id;
        $data['is_default'] = (bool) ($data['is_default'] ?? false);

        if ($data['is_default']) {
            auth()->user()->portfolio->cvs()->update(['is_default' => false]);
        }

        return $data;
    }
}
