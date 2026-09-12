<?php

namespace App\Models;

use App\Support\LocaleCatalog;
use Illuminate\Database\Eloquent\Model;

class Locale extends Model
{
    protected $fillable = [
        'code',
        'name',
        'native_name',
        'is_enabled',
        'is_default',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_enabled' => 'boolean',
            'is_default' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Locale $locale): void {
            $locale->code = strtolower(trim($locale->code));
        });

        static::saved(function (Locale $locale): void {
            if ($locale->is_default) {
                static::query()
                    ->whereKeyNot($locale->id)
                    ->where('is_default', true)
                    ->update(['is_default' => false]);
            }

            LocaleCatalog::forget();
        });

        static::deleted(function (): void {
            LocaleCatalog::forget();
        });
    }
}
