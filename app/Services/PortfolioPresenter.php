<?php

namespace App\Services;

use App\Models\Portfolio;
use App\Support\AppearanceTheme;
use App\Support\LocaleCatalog;
use App\Support\WizardPendingAvatar;

class PortfolioPresenter
{
    public function publicPayload(Portfolio $portfolio, string $locale): array
    {
        $portfolio->load([
            'profile',
            'socialLinks',
            'skillCategories.skills',
            'projects',
            'education',
            'spokenLanguages',
            'principles',
            'cvSettings',
        ]);

        $profile = $portfolio->profile;
        $avatar = WizardPendingAvatar::urlFor($portfolio) ?? $profile?->avatarUrl();

        return [
            'locale' => $locale,
            'username' => $portfolio->slug,
            'theme' => $portfolio->default_theme,
            'appearance' => AppearanceTheme::publicFor($portfolio),
            'seo' => [
                'title' => $portfolio->seo_title ?: ($profile?->full_name.' | Portfolio'),
                'description' => $portfolio->seo_description ?: $profile?->localeText('tagline', $locale),
            ],
            'profile' => $profile ? [
                'full_name' => $profile->full_name,
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
            ] : null,
            'social_links' => $portfolio->socialLinks->map(fn ($link) => [
                'platform' => $link->platform,
                'url' => $link->url,
            ])->values(),
            'skill_categories' => $portfolio->skillCategories->map(fn ($category) => [
                'name' => $category->localeText('name', $locale),
                'skills' => $category->skills->map(fn ($skill) => [
                    'name' => $skill->name,
                    'description' => $skill->localeHtml('description', $locale),
                    'level' => $skill->level,
                ])->values(),
            ])->values(),
            'projects' => $portfolio->projects->map(fn ($project) => [
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
            'principles' => $portfolio->principles->map(fn ($item) => [
                'title' => $item->localeText('title', $locale),
                'description' => $item->localeHtml('description', $locale),
            ])->values(),
            'cv' => [
                'url' => route('portfolio.cv', ['locale' => $locale, 'username' => $portfolio->slug]),
                'pdf_url' => route('portfolio.cv.pdf', ['locale' => $locale, 'username' => $portfolio->slug]),
                'settings' => [
                    ...($portfolio->cvSettings?->only([
                        'template',
                        'show_about',
                        'show_skills',
                        'show_projects',
                        'show_education',
                        'show_languages',
                        'show_principles',
                    ]) ?? []),
                    'show_avatar' => (bool) (($portfolio->cvSettings?->show_avatar ?? true) && $avatar),
                ],
            ],
            'available_locales' => LocaleCatalog::enabled()->map(fn ($item) => [
                'code' => $item->code,
                'label' => strtoupper($item->code),
                'native_name' => $item->native_name,
                'portfolio_url' => route('portfolio.show', ['locale' => $item->code, 'username' => $portfolio->slug]),
                'cv_url' => route('portfolio.cv', ['locale' => $item->code, 'username' => $portfolio->slug]),
            ])->values(),
        ];
    }
}
