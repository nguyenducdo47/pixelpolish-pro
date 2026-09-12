<?php

namespace App\Filament\Forms;

use App\Support\AppearanceTheme;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;

class ThemeAppearanceFields
{
    /**
     * @return array<int, Select|Toggle>
     */
    public static function layout(): array
    {
        return [
            Select::make('layout')
                ->label(__('panel.fields.theme_layout'))
                ->options(fn (): array => AppearanceTheme::layoutOptions())
                ->required(),
            Select::make('hero')
                ->label(__('panel.fields.theme_hero'))
                ->options(fn (): array => AppearanceTheme::heroOptions())
                ->required(),
            Select::make('radius')
                ->label(__('panel.fields.theme_radius'))
                ->options(fn (): array => AppearanceTheme::radiusOptions())
                ->required(),
            Select::make('font')
                ->label(__('panel.fields.theme_font'))
                ->options(fn (): array => AppearanceTheme::fontOptions())
                ->required(),
            Select::make('density')
                ->label(__('panel.fields.theme_density'))
                ->options(fn (): array => AppearanceTheme::densityOptions())
                ->required(),
            Select::make('cv_layout')
                ->label(__('panel.fields.theme_cv_layout'))
                ->options(fn (): array => AppearanceTheme::cvLayoutOptions())
                ->required(),
            Toggle::make('show_particles')
                ->label(__('panel.fields.theme_particles')),
        ];
    }

    /**
     * @return array<int, ColorPicker>
     */
    public static function colors(string $prefix): array
    {
        $labels = [
            'primary' => __('panel.fields.color_primary'),
            'accent' => __('panel.fields.color_accent'),
            'background' => __('panel.fields.color_background'),
            'foreground' => __('panel.fields.color_foreground'),
            'card' => __('panel.fields.color_card'),
            'muted' => __('panel.fields.color_muted'),
            'border' => __('panel.fields.color_border'),
        ];

        return collect($labels)
            ->map(fn (string $label, string $key) => ColorPicker::make($prefix.'.'.$key)
                ->label($label)
                ->hex()
                ->required())
            ->values()
            ->all();
    }
}
