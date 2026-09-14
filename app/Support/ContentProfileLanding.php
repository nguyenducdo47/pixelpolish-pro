<?php

namespace App\Support;

use App\Enums\ContentProfile;
use App\Models\Portfolio;
use App\Support\LocaleCatalog;

class ContentProfileLanding
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public static function profileOptions(string $locale): array
    {
        return collect(ContentProfile::cases())
            ->map(function (ContentProfile $profile) use ($locale): array {
                $config = ContentProfileConfig::forProfile($profile);

                return [
                    'key' => $profile->value,
                    'label' => $profile->label(),
                    'landing' => $config->landingCopy($locale),
                    'demo_url' => self::demoUrlForSlug($config->demoSlug(), $locale),
                    'suggested_theme' => $config->suggestedThemeSlug(),
                ];
            })
            ->values()
            ->all();
    }

    public static function demoUrlForSlug(string $slug, ?string $locale = null): ?string
    {
        $portfolio = Portfolio::query()
            ->where('slug', $slug)
            ->where('is_published', true)
            ->first();

        if (! $portfolio) {
            return null;
        }

        $locale ??= $portfolio->default_locale ?: LocaleCatalog::defaultCode();

        return route('portfolio.show', [
            'locale' => $locale,
            'username' => $portfolio->slug,
        ]);
    }
}
