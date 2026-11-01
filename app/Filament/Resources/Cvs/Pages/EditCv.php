<?php

namespace App\Filament\Resources\Cvs\Pages;

use App\Filament\Resources\Cvs\CvResource;
use App\Support\LocaleCatalog;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;

class EditCv extends EditRecord
{
    protected static string $resource = CvResource::class;

    protected function getHeaderActions(): array
    {
        $record = $this->getRecord();
        $portfolio = auth()->user()->portfolio;

        return LocaleCatalog::enabled()->map(fn ($locale) => Action::make('preview_'.$locale->code)
            ->label(__('panel.actions.preview_cv').' ('.strtoupper($locale->code).')')
            ->icon(Heroicon::OutlinedEye)
            ->url($record->is_default
                ? route('portfolio.cv', ['locale' => $locale->code, 'username' => $portfolio->slug])
                : route('portfolio.cv.show', ['locale' => $locale->code, 'username' => $portfolio->slug, 'cv' => $record->slug]))
            ->openUrlInNewTab())->all();
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (! empty($data['is_default'])) {
            auth()->user()->portfolio->cvs()
                ->where('id', '!=', $this->getRecord()->id)
                ->update(['is_default' => false]);
        }

        return $data;
    }
}
