<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (DB::table('profiles')->select(['id', 'full_name'])->cursor() as $row) {
            $raw = $row->full_name;

            if ($raw === null || $raw === '') {
                continue;
            }

            $decoded = json_decode((string) $raw, true);

            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                continue;
            }

            DB::table('profiles')->where('id', $row->id)->update([
                'full_name' => json_encode([
                    'vi' => (string) $raw,
                    'en' => (string) $raw,
                ], JSON_UNESCAPED_UNICODE),
            ]);
        }

        Schema::table('profiles', function (Blueprint $table) {
            $table->json('full_name')->change();
        });
    }

    public function down(): void
    {
        foreach (DB::table('profiles')->select(['id', 'full_name'])->cursor() as $row) {
            $decoded = json_decode((string) $row->full_name, true);
            $name = is_array($decoded)
                ? (string) ($decoded['vi'] ?? $decoded['en'] ?? reset($decoded) ?: '')
                : (string) $row->full_name;

            DB::table('profiles')->where('id', $row->id)->update([
                'full_name' => $name,
            ]);
        }

        Schema::table('profiles', function (Blueprint $table) {
            $table->string('full_name')->change();
        });
    }
};
