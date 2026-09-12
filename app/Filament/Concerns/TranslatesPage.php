<?php

namespace App\Filament\Concerns;

use Filament\Navigation\NavigationItem;
use Illuminate\Contracts\Support\Htmlable;
use UnitEnum;

trait TranslatesPage
{
    /**
     * @return array<NavigationItem>
     */
    public static function getNavigationItems(): array
    {
        $items = parent::getNavigationItems();

        foreach ($items as $item) {
            $item->label(fn (): string => static::getNavigationLabel());
            $item->group(fn (): string|UnitEnum|null => static::getNavigationGroup());
        }

        return $items;
    }

    public static function getNavigationLabel(): string
    {
        return __(static::$navigationLabel);
    }

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        $group = static::$navigationGroup ?? null;

        if ($group instanceof UnitEnum || $group === null || $group === '') {
            return $group;
        }

        return __($group);
    }

    public function getTitle(): string|Htmlable
    {
        return __(static::$title ?? static::$navigationLabel);
    }
}
