<?php

namespace App\Support;

use App\Models\Locale;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Throwable;

class LocaleCatalog
{
    /**
     * @var Collection<int, Locale>|null
     */
    private static ?Collection $memo = null;

    /**
     * @return Collection<int, Locale>
     */
    public static function enabled(): Collection
    {
        return static::all()->where('is_enabled', true)->values();
    }

    /**
     * @return Collection<int, Locale>
     */
    public static function all(): Collection
    {
        if (static::$memo instanceof Collection) {
            return static::$memo;
        }

        try {
            if (! Schema::hasTable('locales')) {
                return static::$memo = collect();
            }

            return static::$memo = Locale::query()
                ->orderBy('sort_order')
                ->orderBy('code')
                ->get();
        } catch (Throwable) {
            return collect();
        }
    }

    /**
     * @return list<string>
     */
    public static function codes(): array
    {
        return static::enabled()->pluck('code')->all();
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        return static::enabled()
            ->mapWithKeys(fn (Locale $locale) => [$locale->code => $locale->native_name.' ('.$locale->code.')'])
            ->all();
    }

    public static function defaultCode(): string
    {
        return static::enabled()->firstWhere('is_default', true)?->code
            ?? static::enabled()->first()?->code
            ?? 'vi';
    }

    public static function isEnabled(?string $code): bool
    {
        if (! $code) {
            return false;
        }

        return in_array(strtolower($code), static::codes(), true);
    }

    /**
     * @return array<string, string>
     */
    public static function emptyStrings(): array
    {
        return array_fill_keys(static::codes() ?: ['vi'], '');
    }

    /**
     * @return array<string, list<string>>
     */
    public static function emptyLists(): array
    {
        return array_fill_keys(static::codes() ?: ['vi'], []);
    }

    public static function forget(): void
    {
        static::$memo = null;
    }
}
