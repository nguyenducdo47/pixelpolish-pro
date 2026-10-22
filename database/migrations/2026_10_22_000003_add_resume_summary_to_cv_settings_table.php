<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cv_settings', function (Blueprint $table) {
            $table->json('resume_summary')->nullable()->after('template');
            $table->boolean('show_resume_summary')->default(true)->after('resume_summary');
        });
    }

    public function down(): void
    {
        Schema::table('cv_settings', function (Blueprint $table) {
            $table->dropColumn(['resume_summary', 'show_resume_summary']);
        });
    }
};
