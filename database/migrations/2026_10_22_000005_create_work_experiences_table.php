<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('work_experiences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('portfolio_id')->constrained()->cascadeOnDelete();
            $table->json('company');
            $table->json('position');
            $table->json('details')->nullable();
            $table->string('period')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::table('cv_settings', function (Blueprint $table) {
            $table->boolean('show_work_experience')->default(true)->after('show_education');
        });
    }

    public function down(): void
    {
        Schema::table('cv_settings', function (Blueprint $table) {
            $table->dropColumn('show_work_experience');
        });

        Schema::dropIfExists('work_experiences');
    }
};
