<?php

namespace App\Models;

use App\Support\AppearanceTheme;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Theme extends Model
{
    protected $fillable = [
        'slug',
        'name',
        'is_enabled',
        'is_default',
        'sort_order',
        'layout',
        'hero',
        'radius',
        'font',
        'density',
        'cv_layout',
        'show_particles',
        'colors',
        'dark',
    ];

    protected function casts(): array
    {
        return [
            'is_enabled' => 'boolean',
            'is_default' => 'boolean',
            'sort_order' => 'integer',
            'show_particles' => 'boolean',
            'colors' => 'array',
            'dark' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Theme $theme): void {
            if (! filled($theme->slug)) {
                $theme->slug = Str::slug((string) $theme->name) ?: 'theme';
            }

            $theme->slug = Str::slug((string) $theme->slug);

            if ($theme->isDirty('is_enabled') && ! $theme->is_enabled && ! $theme->canBeDisabled()) {
                $theme->is_enabled = true;
            }

            if ($theme->is_default) {
                $theme->is_enabled = true;

                static::query()
                    ->where('id', '!=', $theme->id ?? 0)
                    ->update(['is_default' => false]);
            }
        });
    }

    public function customizations(): HasMany
    {
        return $this->hasMany(PortfolioTheme::class);
    }

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $normalized = AppearanceTheme::normalize([
            'preset' => $this->slug,
            'name' => $this->name,
            'layout' => $this->layout,
            'hero' => $this->hero,
            'radius' => $this->radius,
            'font' => $this->font,
            'density' => $this->density,
            'cv_layout' => $this->cv_layout,
            'show_particles' => $this->show_particles,
            'colors' => $this->colors ?? [],
            'dark' => $this->dark ?? [],
        ], AppearanceTheme::preset('aurora'));

        $normalized['preset'] = $this->slug;
        $normalized['name'] = $this->name;

        return $normalized;
    }

    public static function defaultEnabled(): ?self
    {
        return static::query()->where('is_enabled', true)->where('is_default', true)->orderBy('sort_order')->first()
            ?? static::query()->where('is_enabled', true)->orderBy('sort_order')->orderBy('id')->first();
    }

    /**
     * @return array<int, string>
     */
    public static function enabledOptions(): array
    {
        return static::query()
            ->where('is_enabled', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->pluck('name', 'id')
            ->all();
    }

    public static function seedDefaults(): int
    {
        $created = 0;
        $existing = static::query()->pluck('slug')->all();

        foreach (AppearanceTheme::presets() as $slug => $preset) {
            if (in_array($slug, $existing, true)) {
                continue;
            }

            static::query()->create([
                'slug' => $slug,
                'name' => $preset['name'],
                'is_enabled' => true,
                'is_default' => $slug === 'aurora',
                'sort_order' => $created + 1,
                'layout' => $preset['layout'],
                'hero' => $preset['hero'],
                'radius' => $preset['radius'],
                'font' => $preset['font'],
                'density' => $preset['density'],
                'cv_layout' => $preset['cv_layout'],
                'show_particles' => $preset['show_particles'],
                'colors' => $preset['colors'],
                'dark' => $preset['dark'],
            ]);
            $created++;
        }

        if (! static::query()->where('is_default', true)->exists()) {
            static::query()->where('is_enabled', true)->orderBy('sort_order')->first()?->update(['is_default' => true]);
        }

        return $created;
    }

    public function canBeDisabled(): bool
    {
        if (! $this->is_enabled) {
            return true;
        }

        $query = static::query()->where('is_enabled', true);

        if ($this->exists) {
            $query->where('id', '!=', $this->id);
        }

        return $query->exists();
    }

    public function canBeDeleted(): bool
    {
        return ! $this->is_default && static::query()->count() > 1;
    }
}
