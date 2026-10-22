<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cv_settings', function (Blueprint $table) {
            $table->json('resume_summary_title')->nullable()->after('template');
        });
    }

    public function down(): void
    {
        Schema::table('cv_settings', function (Blueprint $table) {
            $table->dropColumn('resume_summary_title');
        });
    }
};
