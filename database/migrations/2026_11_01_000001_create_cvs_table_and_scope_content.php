<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cvs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('portfolio_id')->constrained()->cascadeOnDelete();
            $table->string('slug');
            $table->json('name')->nullable();
            $table->boolean('is_default')->default(false);
            $table->json('headline')->nullable();
            $table->json('tagline')->nullable();
            $table->json('about')->nullable();
            $table->json('philosophy_quote')->nullable();
            $table->string('template')->default('modern');
            $table->json('resume_summary_title')->nullable();
            $table->json('resume_summary')->nullable();
            $table->boolean('show_resume_summary')->default(true);
            $table->boolean('show_avatar')->default(true);
            $table->boolean('show_about')->default(true);
            $table->boolean('show_skills')->default(true);
            $table->boolean('show_projects')->default(true);
            $table->boolean('show_project_period')->default(true);
            $table->boolean('show_social_links')->default(true);
            $table->boolean('show_certifications')->default(true);
            $table->boolean('show_career_targets')->default(true);
            $table->boolean('show_strengths')->default(true);
            $table->boolean('show_education')->default(true);
            $table->boolean('show_work_experience')->default(true);
            $table->boolean('show_languages')->default(true);
            $table->boolean('show_principles')->default(false);
            $table->timestamps();

            $table->unique(['portfolio_id', 'slug']);
        });

        foreach (['skill_categories', 'projects', 'principles', 'work_experiences', 'cv_list_items'] as $table) {
            Schema::table($table, function (Blueprint $blueprint) use ($table) {
                $blueprint->foreignId('cv_id')->nullable()->after('portfolio_id')->constrained('cvs')->nullOnDelete();
            });
        }

        if (Schema::hasTable('cv_settings')) {
            $settingsRows = DB::table('cv_settings')->get();

            foreach ($settingsRows as $row) {
                DB::table('cvs')->insert([
                    'portfolio_id' => $row->portfolio_id,
                    'slug' => 'default',
                    'name' => json_encode(['vi' => 'CV mặc định', 'en' => 'Default CV']),
                    'is_default' => true,
                    'template' => $row->template,
                    'resume_summary_title' => $row->resume_summary_title ?? null,
                    'resume_summary' => $row->resume_summary ?? null,
                    'show_resume_summary' => $row->show_resume_summary ?? true,
                    'show_avatar' => $row->show_avatar,
                    'show_about' => $row->show_about,
                    'show_skills' => $row->show_skills,
                    'show_projects' => $row->show_projects,
                    'show_project_period' => $row->show_project_period ?? true,
                    'show_social_links' => $row->show_social_links ?? true,
                    'show_certifications' => $row->show_certifications ?? true,
                    'show_career_targets' => $row->show_career_targets ?? true,
                    'show_strengths' => $row->show_strengths ?? true,
                    'show_education' => $row->show_education,
                    'show_work_experience' => $row->show_work_experience ?? true,
                    'show_languages' => $row->show_languages,
                    'show_principles' => $row->show_principles,
                    'created_at' => $row->created_at,
                    'updated_at' => $row->updated_at,
                ]);
            }

            Schema::drop('cv_settings');
        }
    }

    public function down(): void
    {
        Schema::create('cv_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('portfolio_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('template')->default('modern');
            $table->json('resume_summary_title')->nullable();
            $table->json('resume_summary')->nullable();
            $table->boolean('show_resume_summary')->default(true);
            $table->boolean('show_avatar')->default(true);
            $table->boolean('show_about')->default(true);
            $table->boolean('show_skills')->default(true);
            $table->boolean('show_projects')->default(true);
            $table->boolean('show_project_period')->default(true);
            $table->boolean('show_social_links')->default(true);
            $table->boolean('show_certifications')->default(true);
            $table->boolean('show_career_targets')->default(true);
            $table->boolean('show_strengths')->default(true);
            $table->boolean('show_education')->default(true);
            $table->boolean('show_work_experience')->default(true);
            $table->boolean('show_languages')->default(true);
            $table->boolean('show_principles')->default(false);
            $table->timestamps();
        });

        foreach (['skill_categories', 'projects', 'principles', 'work_experiences', 'cv_list_items'] as $table) {
            Schema::table($table, function (Blueprint $blueprint) {
                $blueprint->dropConstrainedForeignId('cv_id');
            });
        }

        Schema::dropIfExists('cvs');
    }
};
