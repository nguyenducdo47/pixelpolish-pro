<?php

namespace App\Support;

use App\Models\Portfolio;
use App\Models\Theme;
use Illuminate\Support\Facades\Schema;
use InvalidArgumentException;
use Throwable;

class AppearanceTheme
{
    public const SCHEMA = 'portfotilo.theme.v1';

    /**
     * @return list<string>
     */
    public static function colorKeys(): array
    {
        return ['primary', 'accent', 'background', 'foreground', 'card', 'muted', 'border'];
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public static function presets(): array
    {
        return [
            'aurora' => [
                'preset' => 'aurora',
                'name' => 'Aurora',
                'layout' => 'centered',
                'hero' => 'particles',
                'radius' => 'lg',
                'font' => 'sans',
                'density' => 'comfortable',
                'show_particles' => true,
                'cv_layout' => 'modern',
                'colors' => [
                    'primary' => '#1f99a8',
                    'accent' => '#248a8f',
                    'background' => '#fafafa',
                    'foreground' => '#141820',
                    'card' => '#ffffff',
                    'muted' => '#5c6673',
                    'border' => '#dce0e5',
                ],
                'dark' => [
                    'primary' => '#2ec4d4',
                    'accent' => '#2aa8b3',
                    'background' => '#0c1016',
                    'foreground' => '#eef2f6',
                    'card' => '#151b24',
                    'muted' => '#8893a3',
                    'border' => '#2b3440',
                ],
            ],
            'midnight' => [
                'preset' => 'midnight',
                'name' => 'Midnight',
                'layout' => 'wide',
                'hero' => 'glow',
                'radius' => 'md',
                'font' => 'sans',
                'density' => 'comfortable',
                'show_particles' => false,
                'cv_layout' => 'modern',
                'colors' => [
                    'primary' => '#4f46e5',
                    'accent' => '#7c3aed',
                    'background' => '#f5f3ff',
                    'foreground' => '#1e1b4b',
                    'card' => '#ffffff',
                    'muted' => '#5b5678',
                    'border' => '#ddd6fe',
                ],
                'dark' => [
                    'primary' => '#818cf8',
                    'accent' => '#c084fc',
                    'background' => '#0b1020',
                    'foreground' => '#e0e7ff',
                    'card' => '#141a2e',
                    'muted' => '#9aa3c7',
                    'border' => '#2a3354',
                ],
            ],
            'sunset' => [
                'preset' => 'sunset',
                'name' => 'Sunset',
                'layout' => 'centered',
                'hero' => 'particles',
                'radius' => 'lg',
                'font' => 'sans',
                'density' => 'comfortable',
                'show_particles' => true,
                'cv_layout' => 'modern',
                'colors' => [
                    'primary' => '#c2410c',
                    'accent' => '#ea580c',
                    'background' => '#fff7ed',
                    'foreground' => '#1c1917',
                    'card' => '#ffffff',
                    'muted' => '#78716c',
                    'border' => '#fed7aa',
                ],
                'dark' => [
                    'primary' => '#fb923c',
                    'accent' => '#f97316',
                    'background' => '#1c1410',
                    'foreground' => '#fff7ed',
                    'card' => '#2a1d16',
                    'muted' => '#d6c3b4',
                    'border' => '#4a3428',
                ],
            ],
            'forest' => [
                'preset' => 'forest',
                'name' => 'Forest',
                'layout' => 'centered',
                'hero' => 'glow',
                'radius' => 'md',
                'font' => 'serif',
                'density' => 'comfortable',
                'show_particles' => false,
                'cv_layout' => 'classic',
                'colors' => [
                    'primary' => '#166534',
                    'accent' => '#3f6212',
                    'background' => '#f7fee7',
                    'foreground' => '#14532d',
                    'card' => '#ffffff',
                    'muted' => '#3f6212',
                    'border' => '#d9f99d',
                ],
                'dark' => [
                    'primary' => '#4ade80',
                    'accent' => '#a3e635',
                    'background' => '#0c1610',
                    'foreground' => '#ecfccb',
                    'card' => '#15241a',
                    'muted' => '#b6d7a8',
                    'border' => '#2a3d2c',
                ],
            ],
            'paper' => [
                'preset' => 'paper',
                'name' => 'Paper',
                'layout' => 'compact',
                'hero' => 'minimal',
                'radius' => 'none',
                'font' => 'serif',
                'density' => 'compact',
                'show_particles' => false,
                'cv_layout' => 'classic',
                'colors' => [
                    'primary' => '#171717',
                    'accent' => '#404040',
                    'background' => '#fafaf9',
                    'foreground' => '#171717',
                    'card' => '#ffffff',
                    'muted' => '#525252',
                    'border' => '#e5e5e5',
                ],
                'dark' => [
                    'primary' => '#f5f5f5',
                    'accent' => '#d4d4d4',
                    'background' => '#0a0a0a',
                    'foreground' => '#f5f5f5',
                    'card' => '#171717',
                    'muted' => '#a3a3a3',
                    'border' => '#2a2a2a',
                ],
            ],
            'ocean' => [
                'preset' => 'ocean',
                'name' => 'Ocean',
                'layout' => 'wide',
                'hero' => 'particles',
                'radius' => 'full',
                'font' => 'sans',
                'density' => 'comfortable',
                'show_particles' => true,
                'cv_layout' => 'modern',
                'colors' => [
                    'primary' => '#0369a1',
                    'accent' => '#0284c7',
                    'background' => '#f0f9ff',
                    'foreground' => '#0c4a6e',
                    'card' => '#ffffff',
                    'muted' => '#0369a1',
                    'border' => '#bae6fd',
                ],
                'dark' => [
                    'primary' => '#38bdf8',
                    'accent' => '#7dd3fc',
                    'background' => '#082f49',
                    'foreground' => '#e0f2fe',
                    'card' => '#0c4a6e',
                    'muted' => '#7dd3fc',
                    'border' => '#155e75',
                ],
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        $options = [];

        foreach (array_keys(static::presets()) as $id) {
            $options[$id] = __('panel.fields.preset_'.$id);
        }

        $options['custom'] = __('panel.fields.preset_custom');

        return $options;
    }

    /**
     * @return array<string, string>
     */
    public static function layoutOptions(): array
    {
        return [
            'centered' => __('panel.fields.layout_centered'),
            'wide' => __('panel.fields.layout_wide'),
            'compact' => __('panel.fields.layout_compact'),
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function heroOptions(): array
    {
        return [
            'particles' => __('panel.fields.hero_particles'),
            'glow' => __('panel.fields.hero_glow'),
            'minimal' => __('panel.fields.hero_minimal'),
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function radiusOptions(): array
    {
        return [
            'none' => __('panel.fields.radius_none'),
            'sm' => __('panel.fields.radius_sm'),
            'md' => __('panel.fields.radius_md'),
            'lg' => __('panel.fields.radius_lg'),
            'full' => __('panel.fields.radius_full'),
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function fontOptions(): array
    {
        return [
            'sans' => __('panel.fields.font_sans'),
            'serif' => __('panel.fields.font_serif'),
            'mono' => __('panel.fields.font_mono'),
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function densityOptions(): array
    {
        return [
            'comfortable' => __('panel.fields.density_comfortable'),
            'compact' => __('panel.fields.density_compact'),
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function cvLayoutOptions(): array
    {
        return [
            'modern' => __('panel.fields.template_modern'),
            'classic' => __('panel.fields.template_classic'),
            'sidebar' => __('panel.fields.template_sidebar'),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function default(): array
    {
        try {
            if (Schema::hasTable('themes')) {
                $theme = Theme::defaultEnabled();

                if ($theme) {
                    return [
                        'preset' => $theme->slug,
                        'name' => $theme->name,
                        'layout' => $theme->layout,
                        'hero' => $theme->hero,
                        'radius' => $theme->radius,
                        'font' => $theme->font,
                        'density' => $theme->density,
                        'cv_layout' => $theme->cv_layout,
                        'show_particles' => $theme->show_particles,
                        'colors' => $theme->colors ?? static::preset('aurora')['colors'],
                        'dark' => $theme->dark ?? static::preset('aurora')['dark'],
                    ];
                }
            }
        } catch (Throwable) {
        }

        return static::preset('aurora');
    }

    /**
     * @return array<string, mixed>
     */
    public static function preset(string $id): array
    {
        return static::presets()[$id] ?? static::presets()['aurora'];
    }

    /**
     * @param  array<string, mixed>|null  $data
     * @return array<string, mixed>
     */
    public static function normalize(?array $data, ?array $base = null): array
    {
        $data ??= [];
        $base ??= static::default();

        return [
            'preset' => filled($data['preset'] ?? null) ? (string) $data['preset'] : (string) ($base['preset'] ?? 'aurora'),
            'name' => filled($data['name'] ?? null) ? (string) $data['name'] : (string) ($base['name'] ?? 'Aurora'),
            'layout' => static::pick($data['layout'] ?? null, ['centered', 'wide', 'compact'], $base['layout'] ?? 'centered'),
            'hero' => static::pick($data['hero'] ?? null, ['particles', 'glow', 'minimal'], $base['hero'] ?? 'particles'),
            'radius' => static::pick($data['radius'] ?? null, ['none', 'sm', 'md', 'lg', 'full'], $base['radius'] ?? 'lg'),
            'font' => static::pick($data['font'] ?? null, ['sans', 'serif', 'mono'], $base['font'] ?? 'sans'),
            'density' => static::pick($data['density'] ?? null, ['comfortable', 'compact'], $base['density'] ?? 'comfortable'),
            'show_particles' => array_key_exists('show_particles', $data)
                ? (bool) $data['show_particles']
                : (bool) ($base['show_particles'] ?? true),
            'cv_layout' => static::pick($data['cv_layout'] ?? null, ['modern', 'classic', 'sidebar'], $base['cv_layout'] ?? 'modern'),
            'colors' => static::normalizeColors($data['colors'] ?? [], $base['colors'] ?? static::preset('aurora')['colors']),
            'dark' => static::normalizeColors($data['dark'] ?? [], $base['dark'] ?? static::preset('aurora')['dark']),
        ];
    }

    /**
     * @param  array<string, mixed>  $base
     * @param  array<string, mixed>  $current
     * @return array<string, mixed>
     */
    public static function customizationDiff(array $base, array $current): array
    {
        $diff = [];

        foreach (['layout', 'hero', 'radius', 'font', 'density', 'cv_layout', 'show_particles'] as $key) {
            if (($current[$key] ?? null) !== ($base[$key] ?? null)) {
                $diff[$key] = $current[$key];
            }
        }

        foreach (['colors', 'dark'] as $group) {
            foreach (static::colorKeys() as $key) {
                if (strtolower((string) ($current[$group][$key] ?? '')) !== strtolower((string) ($base[$group][$key] ?? ''))) {
                    $diff[$group][$key] = $current[$group][$key];
                }
            }
        }

        return $diff;
    }

    public static function assignedTheme(Portfolio $portfolio): ?Theme
    {
        $portfolio->loadMissing('theme');

        if ($portfolio->theme?->is_enabled) {
            return $portfolio->theme;
        }

        return Theme::defaultEnabled();
    }

    /**
     * @return array<string, mixed>
     */
    public static function resolve(Portfolio $portfolio): array
    {
        $theme = static::assignedTheme($portfolio);
        $base = $theme?->definition() ?? static::default();
        $custom = $theme
            ? $portfolio->themeCustomizations()->where('theme_id', $theme->id)->value('customization')
            : null;

        return static::normalize(is_array($custom) ? $custom : [], $base);
    }

    /**
     * @param  array<string, mixed>|string|null  $payload
     * @return array<string, mixed>
     */
    public static function import(array|string|null $payload): array
    {
        if (is_string($payload)) {
            $decoded = json_decode($payload, true);

            if (! is_array($decoded)) {
                throw new InvalidArgumentException(__('panel.notify.theme_import_invalid'));
            }

            $payload = $decoded;
        }

        if (! is_array($payload) || $payload === []) {
            throw new InvalidArgumentException(__('panel.notify.theme_import_invalid'));
        }

        if (isset($payload['theme']) && is_array($payload['theme'])) {
            $payload = $payload['theme'];
        }

        $schema = $payload['schema'] ?? $payload['$schema'] ?? self::SCHEMA;

        if ($schema !== self::SCHEMA) {
            throw new InvalidArgumentException(__('panel.notify.theme_import_invalid'));
        }

        $base = static::default();

        if (isset($payload['preset']) && is_string($payload['preset'])) {
            $catalog = Theme::query()->where('slug', $payload['preset'])->where('is_enabled', true)->first();
            if ($catalog) {
                $base = $catalog->definition();
            }
        }

        return static::normalize($payload, $base);
    }

    /**
     * @param  array<string, mixed>|null  $data
     * @return array<string, mixed>
     */
    public static function export(?array $data): array
    {
        return [
            'schema' => self::SCHEMA,
            ...static::normalize($data),
        ];
    }

    /**
     * @param  array<string, mixed>|null  $data
     */
    public static function apply(Portfolio $portfolio, ?array $data): array
    {
        $data ??= [];
        $catalog = null;

        if (isset($data['theme_id'])) {
            $catalog = Theme::query()->where('id', $data['theme_id'])->where('is_enabled', true)->first();
        }

        $catalog ??= static::assignedTheme($portfolio) ?? Theme::defaultEnabled();

        if (! $catalog) {
            throw new InvalidArgumentException(__('panel.notify.theme_none_enabled'));
        }

        $resolved = static::normalize($data, $catalog->definition());
        $custom = static::customizationDiff($catalog->definition(), $resolved);

        $portfolio->update(['theme_id' => $catalog->id]);
        $portfolio->themeCustomizations()->updateOrCreate(
            ['theme_id' => $catalog->id],
            ['customization' => $custom === [] ? null : $custom],
        );
        $portfolio->cvSettings?->update(['template' => $resolved['cv_layout']]);

        return $resolved;
    }

    public static function syncCvLayout(Portfolio $portfolio, string $layout): void
    {
        $resolved = static::resolve($portfolio);
        $resolved['cv_layout'] = static::pick($layout, ['modern', 'classic', 'sidebar'], $resolved['cv_layout']);
        $resolved['theme_id'] = static::assignedTheme($portfolio)?->id;

        static::apply($portfolio, $resolved);
    }

    /**
     * @param  array<string, mixed>|null  $data
     * @return array<string, mixed>
     */
    public static function public(?array $data): array
    {
        $theme = static::normalize($data);

        return [
            ...$theme,
            'css' => static::cssVariables($theme['colors']),
            'dark_css' => static::cssVariables($theme['dark']),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function publicFor(Portfolio $portfolio): array
    {
        return static::public(static::resolve($portfolio));
    }

    /**
     * @param  array<string, string>  $colors
     * @return array<string, string>
     */
    public static function cssVariables(array $colors): array
    {
        $primary = $colors['primary'];
        $accent = $colors['accent'];

        return [
            '--pf-background' => $colors['background'],
            '--pf-foreground' => $colors['foreground'],
            '--pf-card' => $colors['card'],
            '--pf-card-foreground' => $colors['foreground'],
            '--pf-primary' => $primary,
            '--pf-primary-foreground' => static::contrastColor($primary),
            '--pf-secondary' => $colors['card'],
            '--pf-secondary-foreground' => $colors['foreground'],
            '--pf-muted' => $colors['border'],
            '--pf-muted-foreground' => $colors['muted'],
            '--pf-accent' => $accent,
            '--pf-border' => $colors['border'],
            '--pf-ring' => $primary,
            '--gradient-primary' => 'linear-gradient(135deg, '.$primary.' 0%, '.$accent.' 100%)',
            '--gradient-hero' => 'radial-gradient(ellipse 80% 50% at 50% -20%, color-mix(in srgb, '.$primary.' 14%, transparent), transparent)',
            '--shadow-glow' => '0 0 60px color-mix(in srgb, '.$primary.' 18%, transparent)',
            '--shadow-card' => '0 4px 24px color-mix(in srgb, '.$colors['foreground'].' 10%, transparent)',
        ];
    }

    /**
     * @param  array<string, mixed>  $theme
     */
    public static function matchesPreset(array $theme): bool
    {
        $presetId = $theme['preset'] ?? '';

        if (! isset(static::presets()[$presetId])) {
            return false;
        }

        $preset = static::preset($presetId);

        foreach (['layout', 'hero', 'radius', 'font', 'density', 'cv_layout'] as $key) {
            if (($theme[$key] ?? null) !== $preset[$key]) {
                return false;
            }
        }

        if ((bool) ($theme['show_particles'] ?? false) !== (bool) $preset['show_particles']) {
            return false;
        }

        foreach (['colors', 'dark'] as $group) {
            foreach (static::colorKeys() as $key) {
                if (strtolower((string) ($theme[$group][$key] ?? '')) !== strtolower((string) $preset[$group][$key])) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * @param  list<string>  $allowed
     */
    protected static function pick(mixed $value, array $allowed, string $fallback): string
    {
        return is_string($value) && in_array($value, $allowed, true) ? $value : $fallback;
    }

    /**
     * @param  array<string, mixed>  $input
     * @param  array<string, string>  $fallback
     * @return array<string, string>
     */
    protected static function normalizeColors(array $input, array $fallback): array
    {
        $colors = [];

        foreach (static::colorKeys() as $key) {
            $colors[$key] = static::hex($input[$key] ?? null, $fallback[$key]);
        }

        return $colors;
    }

    protected static function hex(mixed $value, string $fallback): string
    {
        if (! is_string($value)) {
            return $fallback;
        }

        $value = trim($value);

        if (preg_match('/^#([0-9a-f]{3}|[0-9a-f]{6})$/i', $value) === 1) {
            return strtolower($value);
        }

        if (preg_match('/^([0-9a-f]{3}|[0-9a-f]{6})$/i', $value) === 1) {
            return '#'.strtolower($value);
        }

        return $fallback;
    }

    public static function contrastColor(string $hex): string
    {
        $hex = ltrim($hex, '#');

        if (strlen($hex) === 3) {
            $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        }

        $red = hexdec(substr($hex, 0, 2));
        $green = hexdec(substr($hex, 2, 2));
        $blue = hexdec(substr($hex, 4, 2));
        $luminance = (0.299 * $red + 0.587 * $green + 0.114 * $blue) / 255;

        return $luminance > 0.62 ? '#111827' : '#ffffff';
    }
}
