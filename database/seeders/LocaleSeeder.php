<?php

namespace Database\Seeders;

use App\Models\Locale;
use App\Support\LocaleCatalog;
use Illuminate\Database\Seeder;

class LocaleSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['code' => 'vi', 'name' => 'Vietnamese', 'native_name' => 'Tiếng Việt', 'is_enabled' => true, 'is_default' => true, 'sort_order' => 1],
            ['code' => 'en', 'name' => 'English', 'native_name' => 'English', 'is_enabled' => true, 'is_default' => false, 'sort_order' => 2],
        ];

        foreach ($rows as $row) {
            Locale::query()->updateOrCreate(
                ['code' => $row['code']],
                $row
            );
        }

        LocaleCatalog::forget();
    }
}
