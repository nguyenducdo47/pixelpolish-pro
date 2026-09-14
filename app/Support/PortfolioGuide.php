<?php

namespace App\Support;

use App\Filament\Pages\ManageAppearance;
use App\Filament\Pages\ManageCv;
use App\Filament\Pages\ManageProfile;
use App\Filament\Resources\Education\EducationResource;
use App\Filament\Resources\Principles\PrincipleResource;
use App\Filament\Resources\Projects\ProjectResource;
use App\Filament\Resources\SkillCategories\SkillCategoryResource;
use App\Filament\Resources\SocialLinks\SocialLinkResource;
use App\Filament\Resources\SpokenLanguages\SpokenLanguageResource;
use App\Models\Portfolio;
use App\Models\Profile;

class PortfolioGuide
{
    /**
     * @return array{
     *     steps: list<array<string, mixed>>,
     *     steps_done: int,
     *     steps_total: int,
     *     items_done: int,
     *     items_pending: int,
     *     items_total: int,
     *     first_incomplete: string,
     *     content_profile: string,
     *     content_profile_label: string
     * }
     */
    public static function for(Portfolio $portfolio): array
    {
        $portfolio->loadMissing([
            'profile',
            'skillCategories.skills',
            'projects',
            'education',
            'spokenLanguages',
            'socialLinks',
            'principles',
            'cvSettings',
        ]);

        $config = ContentProfileConfig::for($portfolio);
        $profile = $portfolio->profile;
        $hasSkill = $portfolio->skillCategories->contains(fn ($category) => $category->skills->isNotEmpty());
        $hasProject = $portfolio->projects->isNotEmpty();
        $hasEducation = $portfolio->education->isNotEmpty();
        $hasAbout = self::hasCopy($profile, 'about');

        $steps = [
            self::step('profile', ManageProfile::getUrl(), [
                self::item('full_name', ManageProfile::getUrl(), filled($profile?->full_name)),
                self::item('headline', ManageProfile::getUrl(), self::hasCopy($profile, 'headline')),
                self::item('about', ManageProfile::getUrl(), $hasAbout),
                self::item('avatar', ManageProfile::getUrl(), (bool) $profile?->hasAvatar()),
            ]),
        ];

        if ($config->sectionEnabled('skills')) {
            $steps[] = self::step(
                'skills',
                SkillCategoryResource::getUrl(),
                [
                    self::item('skill_category', SkillCategoryResource::getUrl(), $portfolio->skillCategories->isNotEmpty()),
                    self::item('skill', SkillCategoryResource::getUrl(), $hasSkill),
                ],
                $config->panelNavLabel('skills'),
            );
        }

        if ($config->sectionEnabled('projects')) {
            $steps[] = self::step(
                'projects',
                ProjectResource::getUrl(),
                [
                    self::item('project', ProjectResource::getUrl(), $hasProject),
                ],
                $config->panelNavLabel('projects'),
            );
        }

        $steps[] = self::step('education', EducationResource::getUrl(), [
            self::item('education', EducationResource::getUrl(), $hasEducation),
        ]);

        $steps[] = self::step('language', SpokenLanguageResource::getUrl(), [
            self::item('language', SpokenLanguageResource::getUrl(), $portfolio->spokenLanguages->isNotEmpty()),
        ]);

        $presenceItems = [
            self::item('social', SocialLinkResource::getUrl(), $portfolio->socialLinks->isNotEmpty()),
        ];

        if ($config->sectionEnabled('philosophy')) {
            $presenceItems[] = self::item('principle', PrincipleResource::getUrl(), $portfolio->principles->isNotEmpty());
        }

        $steps[] = self::step('presence', SocialLinkResource::getUrl(), $presenceItems);

        $steps[] = self::step('appearance', ManageAppearance::getUrl(), [
            self::item('theme', ManageAppearance::getUrl(), filled($portfolio->theme_id)),
        ]);

        $steps[] = self::step('cv', ManageCv::getUrl(), [
            self::item('cv_content', ManageCv::getUrl(), $hasAbout || $hasSkill || $hasProject || $hasEducation),
        ]);

        $steps[] = self::step('publish', ManageProfile::getUrl(), [
            self::item('publish', ManageProfile::getUrl(), $portfolio->is_published),
        ]);

        $items = collect($steps)->pluck('items')->flatten(1);
        $itemsDone = $items->where('done', true)->count();
        $itemsTotal = $items->count();
        $stepsDone = count(array_filter($steps, fn (array $step): bool => $step['done']));
        $firstIncomplete = collect($steps)->firstWhere('done', false)['key'] ?? $steps[0]['key'];

        return [
            'steps' => $steps,
            'steps_done' => $stepsDone,
            'steps_total' => count($steps),
            'items_done' => $itemsDone,
            'items_pending' => $itemsTotal - $itemsDone,
            'items_total' => $itemsTotal,
            'first_incomplete' => $firstIncomplete,
            'content_profile' => $portfolio->content_profile?->value ?? 'it',
            'content_profile_label' => $portfolio->content_profile?->label() ?? '',
        ];
    }

    /**
     * @param  list<array{key: string, label: string, url: string, done: bool}>  $items
     * @return array{key: string, title: string, url: string, done: bool, items: list<array{key: string, label: string, url: string, done: bool}>}
     */
    protected static function step(string $key, string $url, array $items, ?string $title = null): array
    {
        return [
            'key' => $key,
            'title' => $title ?? __('panel.guide.steps.'.$key.'.title'),
            'url' => $url,
            'done' => $items !== [] && collect($items)->every(fn (array $item): bool => $item['done']),
            'items' => $items,
        ];
    }

    /**
     * @return array{key: string, label: string, url: string, done: bool}
     */
    protected static function item(string $key, string $url, bool $done): array
    {
        return [
            'key' => $key,
            'label' => __('panel.guide.items.'.$key),
            'url' => $url,
            'done' => $done,
        ];
    }

    protected static function hasCopy(?Profile $profile, string $field): bool
    {
        if (! $profile) {
            return false;
        }

        return filled(trim(html_entity_decode(strip_tags($profile->localeText($field)), ENT_QUOTES, 'UTF-8')));
    }
}
