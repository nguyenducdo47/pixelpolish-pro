<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cv_settings', function (Blueprint $table) {
            $table->boolean('show_project_period')->default(true)->after('show_projects');
            $table->boolean('show_social_links')->default(true)->after('show_project_period');
        });
    }

    public function down(): void
    {
        Schema::table('cv_settings', function (Blueprint $table) {
            $table->dropColumn(['show_project_period', 'show_social_links']);
        });
    }
};
