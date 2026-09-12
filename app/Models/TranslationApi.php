<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Throwable;

#[Fillable([
    'name',
    'driver',
    'method',
    'url',
    'user_agent',
    'is_enabled',
    'sort_order',
])]
class TranslationApi extends Model
{
    protected function casts(): array
    {
        return [
            'is_enabled' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    /**
     * @return list<array{name: string, driver: string, method: string, url: string, user_agent: ?string, is_enabled: bool, sort_order: int}>
     */
    public static function defaults(): array
    {
        return [
            [
                'name' => 'Google Chrome',
                'driver' => 'google_chrome',
                'method' => 'GET',
                'url' => 'https://clients5.google.com/translate_a/t',
                'user_agent' => 'Mozilla/5.0',
                'is_enabled' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Google Translate',
                'driver' => 'google',
                'method' => 'GET',
                'url' => 'https://translate.googleapis.com/translate_a/single',
                'user_agent' => null,
                'is_enabled' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'MyMemory',
                'driver' => 'mymemory',
                'method' => 'POST',
                'url' => 'https://api.mymemory.translated.net/get',
                'user_agent' => null,
                'is_enabled' => true,
                'sort_order' => 3,
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function driverOptions(): array
    {
        return [
            'mymemory' => __('panel.fields.driver_mymemory'),
            'google' => __('panel.fields.driver_google'),
            'google_chrome' => __('panel.fields.driver_google_chrome'),
        ];
    }

    /**
     * @return Collection<int, static>
     */
    public static function active(): Collection
    {
        $stored = static::stored();

        if ($stored->isNotEmpty()) {
            return $stored->where('is_enabled', true)->values();
        }

        return static::defaultModels();
    }

    /**
     * @return Collection<int, static>
     */
    public static function stored(): Collection
    {
        if (! Schema::hasTable('translation_apis')) {
            return collect();
        }

        try {
            return static::query()->orderBy('sort_order')->orderBy('id')->get();
        } catch (Throwable) {
            return collect();
        }
    }

    /**
     * @return Collection<int, static>
     */
    public static function defaultModels(): Collection
    {
        return collect(static::defaults())
            ->map(fn (array $row) => (new static)->forceFill($row));
    }

    public static function restoreDefaults(): int
    {
        $created = 0;
        $existing = static::query()->pluck('driver')->all();

        foreach (static::defaults() as $row) {
            if (in_array($row['driver'], $existing, true)) {
                continue;
            }

            static::query()->create($row);
            $created++;
        }

        return $created;
    }

    public function usesPost(): bool
    {
        return strtoupper((string) $this->method) === 'POST';
    }
}
