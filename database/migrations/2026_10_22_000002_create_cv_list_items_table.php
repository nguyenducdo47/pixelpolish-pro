<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cv_list_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('portfolio_id')->constrained()->cascadeOnDelete();
            $table->string('type', 32);
            $table->json('title');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['portfolio_id', 'type', 'sort_order']);
        });

        Schema::table('cv_settings', function (Blueprint $table) {
            $table->boolean('show_certifications')->default(true)->after('show_social_links');
            $table->boolean('show_career_targets')->default(true)->after('show_certifications');
            $table->boolean('show_strengths')->default(true)->after('show_career_targets');
        });
    }

    public function down(): void
    {
        Schema::table('cv_settings', function (Blueprint $table) {
            $table->dropColumn(['show_certifications', 'show_career_targets', 'show_strengths']);
        });

        Schema::dropIfExists('cv_list_items');
    }
};
