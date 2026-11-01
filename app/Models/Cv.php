<?php

namespace App\Models;

use App\Support\HasLocaleText;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cv extends Model
{
    use Concerns\BelongsToPortfolio;
    use HasLocaleText;

    protected $fillable = [
        'portfolio_id',
        'slug',
        'name',
        'is_default',
        'headline',
        'tagline',
        'about',
        'philosophy_quote',
        'template',
        'resume_summary_title',
        'resume_summary',
        'show_resume_summary',
        'show_avatar',
        'show_about',
        'show_skills',
        'show_projects',
        'show_project_period',
        'show_social_links',
        'show_certifications',
        'show_career_targets',
        'show_strengths',
        'show_education',
        'show_work_experience',
        'show_languages',
        'show_principles',
    ];

    protected function casts(): array
    {
        return [
            'name' => 'array',
            'is_default' => 'boolean',
            'headline' => 'array',
            'tagline' => 'array',
            'about' => 'array',
            'philosophy_quote' => 'array',
            'resume_summary_title' => 'array',
            'resume_summary' => 'array',
            'show_resume_summary' => 'boolean',
            'show_avatar' => 'boolean',
            'show_about' => 'boolean',
            'show_skills' => 'boolean',
            'show_projects' => 'boolean',
            'show_project_period' => 'boolean',
            'show_social_links' => 'boolean',
            'show_certifications' => 'boolean',
            'show_career_targets' => 'boolean',
            'show_strengths' => 'boolean',
            'show_education' => 'boolean',
            'show_work_experience' => 'boolean',
            'show_languages' => 'boolean',
            'show_principles' => 'boolean',
        ];
    }

    public function portfolio(): BelongsTo
    {
        return $this->belongsTo(Portfolio::class);
    }

    public function skillCategories(): HasMany
    {
        return $this->hasMany(SkillCategory::class)->orderBy('sort_order');
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class)->orderBy('sort_order');
    }

    public function principles(): HasMany
    {
        return $this->hasMany(Principle::class)->orderBy('sort_order');
    }

    public function workExperiences(): HasMany
    {
        return $this->hasMany(WorkExperience::class)->orderBy('sort_order');
    }

    public function cvListItems(): HasMany
    {
        return $this->hasMany(CvListItem::class)->orderBy('sort_order');
    }

    public function publicUrl(?string $locale = null): string
    {
        $locale ??= $this->portfolio->default_locale;

        if ($this->is_default) {
            return route('portfolio.cv', [
                'locale' => $locale,
                'username' => $this->portfolio->slug,
            ]);
        }

        return route('portfolio.cv.show', [
            'locale' => $locale,
            'username' => $this->portfolio->slug,
            'cv' => $this->slug,
        ]);
    }

    public function pdfUrl(?string $locale = null): string
    {
        $locale ??= $this->portfolio->default_locale;

        if ($this->is_default) {
            return route('portfolio.cv.pdf', [
                'locale' => $locale,
                'username' => $this->portfolio->slug,
            ]);
        }

        return route('portfolio.cv.pdf.show', [
            'locale' => $locale,
            'username' => $this->portfolio->slug,
            'cv' => $this->slug,
        ]);
    }

    public function mergedLocaleText(Portfolio $portfolio, string $field, string $locale): string
    {
        $value = $this->getAttribute($field);

        if (is_array($value) && filled(trim((string) ($value[$locale] ?? $value['vi'] ?? $value['en'] ?? '')))) {
            return $this->localeText($field, $locale);
        }

        return $portfolio->profile?->localeText($field, $locale) ?? '';
    }

    public function mergedLocaleHtml(Portfolio $portfolio, string $field, string $locale): string
    {
        $value = $this->getAttribute($field);

        if (is_array($value) && filled(trim(strip_tags((string) ($value[$locale] ?? $value['vi'] ?? $value['en'] ?? ''))))) {
            return $this->localeHtml($field, $locale);
        }

        return $portfolio->profile?->localeHtml($field, $locale) ?? '';
    }
}
