<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('portfolios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('slug')->unique();
            $table->boolean('is_published')->default(false);
            $table->string('default_locale', 5)->default('vi');
            $table->string('default_theme')->default('system');
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->timestamps();
        });

        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('portfolio_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('full_name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('location')->nullable();
            $table->string('website')->nullable();
            $table->string('avatar_path')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->json('headline')->nullable();
            $table->json('tagline')->nullable();
            $table->json('about')->nullable();
            $table->json('philosophy_quote')->nullable();
            $table->timestamps();
        });

        Schema::create('social_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('portfolio_id')->constrained()->cascadeOnDelete();
            $table->string('platform');
            $table->string('url');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('skill_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('portfolio_id')->constrained()->cascadeOnDelete();
            $table->json('name');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('skills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('skill_category_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->json('description')->nullable();
            $table->unsignedTinyInteger('level')->default(50);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('portfolio_id')->constrained()->cascadeOnDelete();
            $table->json('title');
            $table->json('subtitle')->nullable();
            $table->json('complexity')->nullable();
            $table->json('summary')->nullable();
            $table->json('problem')->nullable();
            $table->json('solution')->nullable();
            $table->json('learned')->nullable();
            $table->json('highlights')->nullable();
            $table->json('tech_stack')->nullable();
            $table->string('period')->nullable();
            $table->string('demo_url')->nullable();
            $table->string('demo_label')->nullable();
            $table->string('github_url')->nullable();
            $table->boolean('is_featured')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('education', function (Blueprint $table) {
            $table->id();
            $table->foreignId('portfolio_id')->constrained()->cascadeOnDelete();
            $table->json('degree');
            $table->json('school');
            $table->json('details')->nullable();
            $table->string('period')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('spoken_languages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('portfolio_id')->constrained()->cascadeOnDelete();
            $table->json('name');
            $table->json('level')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('principles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('portfolio_id')->constrained()->cascadeOnDelete();
            $table->json('title');
            $table->json('description')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('cv_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('portfolio_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('template')->default('modern');
            $table->boolean('show_avatar')->default(true);
            $table->boolean('show_about')->default(true);
            $table->boolean('show_skills')->default(true);
            $table->boolean('show_projects')->default(true);
            $table->boolean('show_education')->default(true);
            $table->boolean('show_languages')->default(true);
            $table->boolean('show_principles')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cv_settings');
        Schema::dropIfExists('principles');
        Schema::dropIfExists('spoken_languages');
        Schema::dropIfExists('education');
        Schema::dropIfExists('projects');
        Schema::dropIfExists('skills');
        Schema::dropIfExists('skill_categories');
        Schema::dropIfExists('social_links');
        Schema::dropIfExists('profiles');
        Schema::dropIfExists('portfolios');
    }
};
