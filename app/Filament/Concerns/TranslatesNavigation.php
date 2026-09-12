<?php

namespace App\Filament\Concerns;

use Filament\Navigation\NavigationItem;
use UnitEnum;

trait TranslatesNavigation
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

    public static function getModelLabel(): string
    {
        return static::getNavigationLabel();
    }

    public static function getPluralModelLabel(): string
    {
        return static::getNavigationLabel();
    }

    public static function getTitleCaseModelLabel(): string
    {
        return static::getModelLabel();
    }

    public static function getTitleCasePluralModelLabel(): string
    {
        return static::getPluralModelLabel();
    }
}
