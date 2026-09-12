<?php

use App\Models\Portfolio;
use App\Models\Theme;
use App\Support\AppearanceTheme;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('themes', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');
            $table->boolean('is_enabled')->default(true);
            $table->boolean('is_default')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->string('layout')->default('centered');
            $table->string('hero')->default('particles');
            $table->string('radius')->default('lg');
            $table->string('font')->default('sans');
            $table->string('density')->default('comfortable');
            $table->string('cv_layout')->default('modern');
            $table->boolean('show_particles')->default(true);
            $table->json('colors');
            $table->json('dark');
            $table->timestamps();
        });

        Schema::create('portfolio_themes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('portfolio_id')->constrained()->cascadeOnDelete();
            $table->foreignId('theme_id')->constrained()->cascadeOnDelete();
            $table->json('customization')->nullable();
            $table->timestamps();

            $table->unique(['portfolio_id', 'theme_id']);
        });

        Schema::table('portfolios', function (Blueprint $table) {
            $table->foreignId('theme_id')->nullable()->after('default_theme')->constrained('themes')->nullOnDelete();
        });

        Theme::seedDefaults();
        $this->migrateExistingAppearance();
    }

    public function down(): void
    {
        Schema::table('portfolios', function (Blueprint $table) {
            $table->dropConstrainedForeignId('theme_id');
        });

        Schema::dropIfExists('portfolio_themes');
        Schema::dropIfExists('themes');
    }

    protected function migrateExistingAppearance(): void
    {
        if (! Schema::hasColumn('portfolios', 'appearance')) {
            return;
        }

        Portfolio::query()->withoutGlobalScopes()->each(function (Portfolio $portfolio): void {
            $appearance = is_array($portfolio->appearance) ? $portfolio->appearance : [];
            $slug = (string) ($appearance['preset'] ?? 'aurora');
            $theme = Theme::query()->where('slug', $slug)->first() ?? Theme::defaultEnabled();

            if (! $theme) {
                return;
            }

            $portfolio->forceFill(['theme_id' => $theme->id])->save();

            $normalized = AppearanceTheme::normalize($appearance, $theme->definition());
            $custom = AppearanceTheme::customizationDiff($theme->definition(), $normalized);

            if ($custom !== []) {
                $portfolio->themeCustomizations()->updateOrCreate(
                    ['theme_id' => $theme->id],
                    ['customization' => $custom],
                );
            }
        });
    }
};
