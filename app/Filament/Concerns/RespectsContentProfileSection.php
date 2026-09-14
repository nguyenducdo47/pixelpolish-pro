<?php

namespace App\Filament\Concerns;

use App\Support\ContentProfileConfig;

trait RespectsContentProfileSection
{
    /**
     * Section key from config/content_profiles.php (`skills`, `projects`, `philosophy`, …).
     * Override in the resource class; return null to always show.
     */
    protected static function contentProfileSectionKey(): ?string
    {
        return null;
    }

    public static function shouldRegisterNavigation(): bool
    {
        if (! static::contentProfileSectionVisible()) {
            return false;
        }

        if (! method_exists(parent::class, 'shouldRegisterNavigation')) {
            return true;
        }

        return parent::shouldRegisterNavigation();
    }

    public static function canAccess(): bool
    {
        if (! static::contentProfileSectionVisible()) {
            return false;
        }

        return parent::canAccess();
    }

    protected static function contentProfileSectionVisible(): bool
    {
        $section = static::contentProfileSectionKey();

        if ($section === null || $section === '') {
            return true;
        }

        return ContentProfileConfig::for(auth()->user()?->portfolio)->sectionEnabled($section);
    }
}
