<?php

namespace Database\Seeders\Concerns;

use App\Enums\CvListType;
use App\Models\Cv;
use App\Models\Portfolio;

trait SeedsCvStrengthListItems
{
    /**
     * @param  array<string, mixed>  $vi
     * @param  array<string, mixed>  $en
     */
    protected function seedCvStrengthsFromAboutHighlights(Portfolio $portfolio, Cv $cv, array $vi, array $en): void
    {
        $portfolio->cvListItems()
            ->where('cv_id', $cv->id)
            ->where('type', CvListType::Strength)
            ->delete();

        foreach ($vi['about']['highlights'] ?? [] as $index => $item) {
            $enItem = $en['about']['highlights'][$index] ?? $item;

            $portfolio->cvListItems()->create([
                'cv_id' => $cv->id,
                'type' => CvListType::Strength,
                'title' => [
                    'vi' => trim(($item['title'] ?? '').': '.($item['description'] ?? '')),
                    'en' => trim(($enItem['title'] ?? '').': '.($enItem['description'] ?? '')),
                ],
                'sort_order' => $index,
            ]);
        }
    }
}
