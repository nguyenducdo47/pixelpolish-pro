<?php

namespace App\Services;

use App\Enums\CvListType;
use App\Models\Cv;
use App\Models\Portfolio;
use App\Enums\ContentProfile;
use App\Support\ContentProfileConfig;
use App\Support\AppearanceTheme;
use App\Support\LocaleCatalog;
use App\Support\WizardPendingAvatar;

class PortfolioPresenter
{
    public function publicPayload(Portfolio $portfolio, string $locale, ?Cv $cvDocument = null): array
    {
        $portfolio->load([
            'profile',
            'socialLinks',
            'cvs',
            'defaultCv',
        ]);

        if ($cvDocument !== null) {
            return $this->cvPayload($portfolio, $locale, $cvDocument);
        }

        $portfolio->load([
            'skillCategories' => fn ($query) => $query->whereNull('cv_id')->orderBy('sort_order'),
            'skillCategories.skills',
            'projects' => fn ($query) => $query->whereNull('cv_id')->orderBy('sort_order'),
            'education',
            'spokenLanguages',
            'principles' => fn ($query) => $query->whereNull('cv_id')->orderBy('sort_order'),
        ]);

        $profile = $portfolio->profile;
        $avatar = WizardPendingAvatar::urlFor($portfolio) ?? $profile?->avatarUrl();
        $contentConfig = ContentProfileConfig::for($portfolio);
        $defaultCv = $portfolio->defaultCv;

        $skillCategories = $contentConfig->sectionEnabled('skills')
            ? $portfolio->skillCategories
            : collect();

        $projects = $contentConfig->sectionEnabled('projects')
            ? $portfolio->projects
            : collect();

        $principles = $contentConfig->sectionEnabled('philosophy')
            ? $portfolio->principles
            : collect();

        return [
            ...$this->basePayload($portfolio, $locale, $profile, $avatar, $contentConfig, $skillCategories, $projects, $principles),
            'work_experiences' => collect(),
            'certifications' => collect(),
            'career_targets' => collect(),
            'strengths' => collect(),
            'cv' => $this->cvMeta($portfolio, $locale, $defaultCv, $contentConfig, $avatar),
            'available_locales' => $this->availableLocales($portfolio, $defaultCv),
        ];
    }

    public function cvPayload(Portfolio $portfolio, string $locale, Cv $cvDocument): array
    {
        $scope = $cvDocument->is_default
            ? fn ($query) => $query->whereNull('cv_id')
            : fn ($query) => $query->where('cv_id', $cvDocument->id);

        $portfolio->load([
            'skillCategories' => fn ($query) => $scope($query)->orderBy('sort_order'),
            'skillCategories.skills',
            'projects' => fn ($query) => $scope($query)->orderBy('sort_order'),
            'education',
            'workExperiences' => fn ($query) => $this->scopeWorkExperiencesForCv($query, $cvDocument),
            'spokenLanguages',
            'principles' => fn ($query) => $scope($query)->orderBy('sort_order'),
            'cvListItems' => fn ($query) => $this->scopeCvListItemsForCv($query, $cvDocument),
        ]);

        $profile = $portfolio->profile;
        $avatar = WizardPendingAvatar::urlFor($portfolio) ?? $profile?->avatarUrl();
        $contentConfig = ContentProfileConfig::for($portfolio);

        $skillCategories = $contentConfig->sectionEnabled('skills')
            ? $portfolio->skillCategories
            : collect();

        $projects = $contentConfig->sectionEnabled('projects')
            ? $portfolio->projects
            : collect();

        $principles = $contentConfig->sectionEnabled('philosophy')
            ? $portfolio->principles
            : collect();

        $profilePayload = $profile ? [
            'full_name' => $profile->localeText('full_name', $locale),
            'email' => $profile->email,
            'phone' => $profile->phone,
            'location' => $profile->location,
            'website' => $profile->website,
            'avatar' => $avatar,
            'date_of_birth' => $profile->date_of_birth?->format('d/m/Y'),
            'headline' => $cvDocument->mergedLocaleText($portfolio, 'headline', $locale),
            'tagline' => $cvDocument->mergedLocaleText($portfolio, 'tagline', $locale),
            'about' => $cvDocument->mergedLocaleHtml($portfolio, 'about', $locale),
            'philosophy_quote' => $cvDocument->mergedLocaleHtml($portfolio, 'philosophy_quote', $locale),
        ] : null;

        return [
            ...$this->basePayload($portfolio, $locale, $profile, $avatar, $contentConfig, $skillCategories, $projects, $principles, $profilePayload),
            'work_experiences' => $portfolio->workExperiences->map(fn ($item) => [
                'company' => $item->localeText('company', $locale),
                'position' => $item->localeText('position', $locale),
                'details' => $item->localeHtml('details', $locale),
                'period' => $item->period,
            ])->values()->all(),
            'certifications' => $this->mapCvListItems($portfolio, CvListType::Certification, $locale, $cvDocument)->all(),
            'career_targets' => $this->mapCvListItems($portfolio, CvListType::CareerTarget, $locale, $cvDocument)->all(),
            'strengths' => $this->mapCvListItems($portfolio, CvListType::Strength, $locale, $cvDocument)->all(),
            'cv' => $this->cvMeta($portfolio, $locale, $cvDocument, $contentConfig, $avatar),
            'available_locales' => $this->availableLocales($portfolio, $cvDocument),
        ];
    }

    /**
     * @param  \Illuminate\Support\Collection<int, mixed>  $skillCategories
     * @param  \Illuminate\Support\Collection<int, mixed>  $projects
     * @param  \Illuminate\Support\Collection<int, mixed>  $principles
     * @param  array<string, mixed>|null  $profileOverride
     * @return array<string, mixed>
     */
    private function basePayload(
        Portfolio $portfolio,
        string $locale,
        $profile,
        ?string $avatar,
        ContentProfileConfig $contentConfig,
        $skillCategories,
        $projects,
        $principles,
        ?array $profileOverride = null,
    ): array {
        $profilePayload = $profileOverride ?? ($profile ? [
            'full_name' => $profile->localeText('full_name', $locale),
            'email' => $profile->email,
            'phone' => $profile->phone,
            'location' => $profile->location,
            'website' => $profile->website,
            'avatar' => $avatar,
            'date_of_birth' => $profile->date_of_birth?->format('d/m/Y'),
            'headline' => $profile->localeText('headline', $locale),
            'tagline' => $profile->localeText('tagline', $locale),
            'about' => $profile->localeHtml('about', $locale),
            'philosophy_quote' => $profile->localeHtml('philosophy_quote', $locale),
        ] : null);

        return [
            'locale' => $locale,
            'username' => $portfolio->slug,
            'content_profile' => $portfolio->content_profile?->value ?? ContentProfile::It->value,
            'display' => $contentConfig->toPublicArray(),
            'ui' => $contentConfig->mergedUi($locale),
            'theme' => $portfolio->default_theme,
            'appearance' => AppearanceTheme::publicFor($portfolio),
            'seo' => [
                'title' => $portfolio->seo_title ?: ($profile?->localeText('full_name', $locale).' | Portfolio'),
                'description' => $portfolio->seo_description ?: ($profilePayload['tagline'] ?? $profile?->localeText('tagline', $locale)),
            ],
            'profile' => $profilePayload,
            'social_links' => $portfolio->socialLinks->map(fn ($link) => [
                'platform' => $link->platform,
                'url' => $link->url,
            ])->values(),
            'skill_categories' => $skillCategories->map(fn ($category) => [
                'name' => $category->localeText('name', $locale),
                'skills' => $category->skills->map(fn ($skill) => [
                    'name' => $skill->name,
                    'description' => $skill->localeHtml('description', $locale),
                    'level' => $skill->level,
                ])->values(),
            ])->values(),
            'projects' => $projects->map(fn ($project) => [
                'title' => $project->localeText('title', $locale),
                'subtitle' => $project->localeText('subtitle', $locale),
                'complexity' => $project->localeText('complexity', $locale),
                'summary' => $project->localeHtml('summary', $locale),
                'problem' => $project->localeHtml('problem', $locale),
                'solution' => $project->localeHtml('solution', $locale),
                'learned' => $project->localeHtml('learned', $locale),
                'highlights' => $project->localeList('highlights', $locale),
                'tech_stack' => $project->tech_stack ?? [],
                'period' => $project->period,
                'demo_url' => $project->demo_url,
                'demo_label' => $project->demo_label,
                'github_url' => $project->github_url,
            ])->values(),
            'education' => $portfolio->education->map(fn ($item) => [
                'degree' => $item->localeText('degree', $locale),
                'school' => $item->localeText('school', $locale),
                'details' => $item->localeHtml('details', $locale),
                'period' => $item->period,
            ])->values(),
            'languages' => $portfolio->spokenLanguages->map(fn ($item) => [
                'name' => $item->localeText('name', $locale),
                'level' => $item->localeText('level', $locale),
            ])->values(),
            'principles' => $principles->map(fn ($item) => [
                'title' => $item->localeText('title', $locale),
                'description' => $item->localeHtml('description', $locale),
            ])->values(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function cvMeta(
        Portfolio $portfolio,
        string $locale,
        ?Cv $cvDocument,
        ContentProfileConfig $contentConfig,
        ?string $avatar,
    ): array {
        if (! $cvDocument) {
            return [
                'slug' => null,
                'name' => null,
                'url' => route('portfolio.cv', ['locale' => $locale, 'username' => $portfolio->slug]),
                'pdf_url' => route('portfolio.cv.pdf', ['locale' => $locale, 'username' => $portfolio->slug]),
                'resume_summary_title' => null,
                'resume_summary' => null,
                'settings' => [],
            ];
        }

        return [
            'slug' => $cvDocument->slug,
            'name' => $cvDocument->localeText('name', $locale) ?: null,
            'url' => $cvDocument->publicUrl($locale),
            'pdf_url' => $cvDocument->pdfUrl($locale),
            'resume_summary_title' => ($cvDocument->show_resume_summary ?? true)
                ? ($cvDocument->localeText('resume_summary_title', $locale) ?: null)
                : null,
            'resume_summary' => ($cvDocument->show_resume_summary ?? true)
                ? ($cvDocument->localeText('resume_summary', $locale) ?: null)
                : null,
            'settings' => [
                ...($cvDocument->only([
                    'template',
                    'show_resume_summary',
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
                ])),
                'show_skills' => (bool) (($cvDocument->show_skills ?? true) && $contentConfig->sectionEnabled('skills')),
                'show_projects' => (bool) (($cvDocument->show_projects ?? true) && $contentConfig->sectionEnabled('projects')),
                'show_principles' => (bool) (($cvDocument->show_principles ?? false) && $contentConfig->sectionEnabled('philosophy')),
                'show_avatar' => (bool) (($cvDocument->show_avatar ?? true) && $avatar),
            ],
        ];
    }

    private function availableLocales(Portfolio $portfolio, ?Cv $cvDocument): \Illuminate\Support\Collection
    {
        return LocaleCatalog::enabled()->map(function ($item) use ($portfolio, $cvDocument) {
            $cv = $cvDocument;

            return [
                'code' => $item->code,
                'label' => strtoupper($item->code),
                'native_name' => $item->native_name,
                'portfolio_url' => route('portfolio.show', ['locale' => $item->code, 'username' => $portfolio->slug]),
                'cv_url' => $cv
                    ? ($cv->is_default
                        ? route('portfolio.cv', ['locale' => $item->code, 'username' => $portfolio->slug])
                        : route('portfolio.cv.show', ['locale' => $item->code, 'username' => $portfolio->slug, 'cv' => $cv->slug]))
                    : route('portfolio.cv', ['locale' => $item->code, 'username' => $portfolio->slug]),
            ];
        })->values();
    }

    private function mapCvListItems(Portfolio $portfolio, CvListType $type, string $locale, Cv $cvDocument): \Illuminate\Support\Collection
    {
        return $portfolio->cvListItems
            ->filter(fn ($item) => $item->type === $type)
            ->values()
            ->map(fn ($item) => [
                'title' => $item->localeText('title', $locale),
            ]);
    }

    private function scopeWorkExperiencesForCv($query, Cv $cvDocument)
    {
        if ($cvDocument->is_default) {
            return $query
                ->where(function ($inner) use ($cvDocument): void {
                    $inner->whereNull('cv_id')->orWhere('cv_id', $cvDocument->id);
                })
                ->orderBy('sort_order');
        }

        return $query->where('cv_id', $cvDocument->id)->orderBy('sort_order');
    }

    private function scopeCvListItemsForCv($query, Cv $cvDocument)
    {
        if ($cvDocument->is_default) {
            return $query
                ->where(function ($inner) use ($cvDocument): void {
                    $inner->whereNull('cv_id')->orWhere('cv_id', $cvDocument->id);
                })
                ->orderBy('sort_order');
        }

        return $query->where('cv_id', $cvDocument->id)->orderBy('sort_order');
    }
}
