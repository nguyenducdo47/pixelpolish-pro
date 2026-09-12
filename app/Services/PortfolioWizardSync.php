<?php

namespace App\Services;

use App\Models\Portfolio;
use App\Models\Profile;
use App\Support\AppearanceTheme;
use App\Support\RichText;
use App\Support\UiLocale;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

class PortfolioWizardSync
{
    /**
     * @return array<string, mixed>
     */
    public function formState(Portfolio $portfolio): array
    {
        $portfolio->load([
            'profile',
            'skillCategories.skills',
            'projects',
            'education',
            'spokenLanguages',
            'socialLinks',
            'principles',
            'cvSettings',
        ]);

        $profile = $portfolio->profile;
        $cv = $portfolio->cvSettings;

        return [
            '_locale' => UiLocale::current(),
            'username' => $portfolio->user?->username ?? $portfolio->slug,
            'is_published' => $portfolio->is_published,
            'default_locale' => $portfolio->default_locale,
            'default_theme' => $portfolio->default_theme,
            'seo_title' => $portfolio->seo_title,
            'seo_description' => $portfolio->seo_description,
            'full_name' => $profile?->full_name,
            'email' => $profile?->email,
            'phone' => $profile?->phone,
            'location' => $profile?->location,
            'website' => $profile?->website,
            'date_of_birth' => $profile?->date_of_birth?->toDateString(),
            'avatar_path' => $profile?->resolvedAvatarPath(),
            'headline' => $profile?->headline ?? [],
            'tagline' => $profile?->tagline ?? [],
            'about' => $profile?->about ?? [],
            'philosophy_quote' => $profile?->philosophy_quote ?? [],
            'skill_categories' => $portfolio->skillCategories->map(fn ($category) => [
                'id' => $category->id,
                'name' => $category->name,
                'skills' => $category->skills->map(fn ($skill) => [
                    'id' => $skill->id,
                    'name' => $skill->name,
                    'level' => $skill->level,
                    'description' => $skill->description,
                ])->values()->all(),
            ])->values()->all(),
            'projects' => $portfolio->projects->map(fn ($project) => $project->only([
                'id',
                'title',
                'subtitle',
                'complexity',
                'summary',
                'problem',
                'solution',
                'learned',
                'highlights',
                'tech_stack',
                'period',
                'demo_url',
                'demo_label',
                'github_url',
                'is_featured',
            ]))->values()->all(),
            'education' => $portfolio->education->map(fn ($item) => $item->only([
                'id',
                'degree',
                'school',
                'details',
                'period',
            ]))->values()->all(),
            'languages' => $portfolio->spokenLanguages->map(fn ($item) => $item->only([
                'id',
                'name',
                'level',
            ]))->values()->all(),
            'social_links' => $portfolio->socialLinks->map(fn ($item) => $item->only([
                'id',
                'platform',
                'url',
            ]))->values()->all(),
            'principles' => $portfolio->principles->map(fn ($item) => $item->only([
                'id',
                'title',
                'description',
            ]))->values()->all(),
            'cv_template' => $cv?->template ?? 'modern',
            'show_avatar' => $cv?->show_avatar ?? true,
            'show_about' => $cv?->show_about ?? true,
            'show_skills' => $cv?->show_skills ?? true,
            'show_projects' => $cv?->show_projects ?? true,
            'show_education' => $cv?->show_education ?? true,
            'show_languages' => $cv?->show_languages ?? true,
            'show_principles' => $cv?->show_principles ?? false,
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function saveAll(Portfolio $portfolio, array $data): void
    {
        $this->saveProfile($portfolio, $data);

        if (array_key_exists('skill_categories', $data)) {
            $this->saveSkills($portfolio, $data['skill_categories'] ?? []);
        }

        if (array_key_exists('projects', $data)) {
            $this->sync($portfolio->projects(), $data['projects'] ?? [], [
                'title', 'subtitle', 'complexity', 'summary', 'problem', 'solution',
                'learned', 'highlights', 'tech_stack', 'period', 'demo_url',
                'demo_label', 'github_url', 'is_featured',
            ], ['title', 'subtitle', 'complexity', 'summary', 'problem', 'solution', 'learned', 'highlights']);
        }

        if (array_key_exists('education', $data)) {
            $this->sync($portfolio->education(), $data['education'] ?? [], [
                'degree', 'school', 'details', 'period',
            ], ['degree', 'school', 'details']);
        }

        if (array_key_exists('languages', $data)) {
            $this->sync($portfolio->spokenLanguages(), $data['languages'] ?? [], [
                'name', 'level',
            ], ['name', 'level']);
        }

        if (array_key_exists('social_links', $data)) {
            $this->sync($portfolio->socialLinks(), $data['social_links'] ?? [], [
                'platform', 'url',
            ]);
        }

        if (array_key_exists('principles', $data)) {
            $this->sync($portfolio->principles(), $data['principles'] ?? [], [
                'title', 'description',
            ], ['title', 'description']);
        }

        $cvTemplate = $data['cv_template'] ?? 'modern';

        $portfolio->cvSettings?->update([
            'template' => $cvTemplate,
            'show_avatar' => (bool) ($data['show_avatar'] ?? true),
            'show_about' => (bool) ($data['show_about'] ?? true),
            'show_skills' => (bool) ($data['show_skills'] ?? true),
            'show_projects' => (bool) ($data['show_projects'] ?? true),
            'show_education' => (bool) ($data['show_education'] ?? true),
            'show_languages' => (bool) ($data['show_languages'] ?? true),
            'show_principles' => (bool) ($data['show_principles'] ?? true),
        ]);

        AppearanceTheme::syncCvLayout($portfolio, (string) $cvTemplate);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function saveProfile(Portfolio $portfolio, array $data): void
    {
        $data = $this->applyLocaleInputs($data, ['headline', 'tagline', 'about', 'philosophy_quote']);
        $user = $portfolio->user;

        if ($user && filled($data['username'] ?? null)) {
            validator($data, [
                'username' => ['required', 'alpha_dash', Rule::unique('users', 'username')->ignore($user->id)],
            ])->validate();

            $user->update([
                'username' => $data['username'],
                'name' => $data['full_name'] ?? $user->name,
            ]);
        }

        $portfolio->update([
            'slug' => $data['username'] ?? $portfolio->slug,
            'is_published' => (bool) ($data['is_published'] ?? false),
            'default_locale' => $data['default_locale'] ?? $portfolio->default_locale,
            'default_theme' => $data['default_theme'] ?? $portfolio->default_theme,
            'seo_title' => $data['seo_title'] ?? null,
            'seo_description' => $data['seo_description'] ?? null,
        ]);

        $profilePayload = [
            'full_name' => $data['full_name'] ?? $portfolio->profile->full_name,
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'location' => $data['location'] ?? null,
            'website' => $data['website'] ?? null,
            'date_of_birth' => $data['date_of_birth'] ?? null,
            'headline' => $data['headline'] ?? [],
            'tagline' => $data['tagline'] ?? [],
            'about' => $data['about'] ?? [],
            'philosophy_quote' => $data['philosophy_quote'] ?? [],
        ];

        if (array_key_exists('avatar_path', $data)) {
            $profilePayload['avatar_path'] = Profile::storedAvatarPath($data['avatar_path']);
        }

        $portfolio->profile?->update($profilePayload);
    }

    /**
     * @param  array<int, array<string, mixed>>  $rows
     */
    public function saveSkills(Portfolio $portfolio, array $rows): void
    {
        $keepCategories = [];

        foreach (array_values($rows) as $index => $row) {
            $row = $this->applyLocaleInputs($row, ['name']);
            $payload = [
                'name' => $row['name'] ?? [],
                'sort_order' => $index,
            ];

            $category = null;
            if (filled($row['id'] ?? null)) {
                $category = $portfolio->skillCategories()->whereKey($row['id'])->first();
            }

            if ($category) {
                $category->update($payload);
            } else {
                $category = $portfolio->skillCategories()->create($payload);
            }

            $keepCategories[] = $category->id;
            $keepSkills = [];

            foreach (array_values($row['skills'] ?? []) as $skillIndex => $skillRow) {
                $skillRow = $this->applyLocaleInputs($skillRow, ['description']);
                $skillPayload = [
                    'name' => $skillRow['name'] ?? '',
                    'level' => (int) ($skillRow['level'] ?? 50),
                    'description' => $skillRow['description'] ?? [],
                    'sort_order' => $skillIndex,
                ];

                $skill = null;
                if (filled($skillRow['id'] ?? null)) {
                    $skill = $category->skills()->whereKey($skillRow['id'])->first();
                }

                if ($skill) {
                    $skill->update($skillPayload);
                } else {
                    $skill = $category->skills()->create($skillPayload);
                }

                $keepSkills[] = $skill->id;
            }

            $category->skills()->whereNotIn('id', $keepSkills)->delete();
        }

        $portfolio->skillCategories()->whereNotIn('id', $keepCategories)->delete();
    }

    /**
     * @param  array<int, array<string, mixed>>  $rows
     * @param  array<int, string>  $fields
     * @param  array<int, string>  $localeFields
     */
    protected function sync(mixed $relation, array $rows, array $fields, array $localeFields = []): void
    {
        $keep = [];
        $scoped = fn () => $relation->getRelated()->newQuery()
            ->where($relation->getForeignKeyName(), $relation->getParent()->getKey());

        foreach (array_values($rows) as $index => $row) {
            if ($localeFields !== []) {
                $row = $this->applyLocaleInputs($row, $localeFields);
            }

            $payload = Arr::only($row, $fields);
            $payload['sort_order'] = $index;

            $record = filled($row['id'] ?? null)
                ? $scoped()->whereKey($row['id'])->first()
                : null;

            if ($record) {
                $record->update($payload);
            } else {
                $record = $relation->create($payload);
            }

            $keep[] = $record->id;
        }

        if ($keep === []) {
            $scoped()->delete();

            return;
        }

        $scoped()->whereNotIn('id', $keep)->delete();
    }

    /**
     * @param  array<string, mixed>  $row
     * @param  array<int, string>  $names
     * @return array<string, mixed>
     */
    protected function applyLocaleInputs(array $row, array $names): array
    {
        $locale = $row['_locale'] ?? UiLocale::current();
        $current = is_array($row['_current'] ?? null) ? $row['_current'] : [];

        foreach ($names as $name) {
            $bag = is_array($row[$name] ?? null) ? $row[$name] : [];

            foreach ($bag as $code => $stored) {
                $bag[$code] = $this->normalizeLocaleStoredValue($stored);
            }

            if (array_key_exists($name, $current) && filled($current[$name])) {
                $bag[$locale] = $this->normalizeLocaleStoredValue($current[$name]);
            }

            $row[$name] = $bag;
        }

        unset($row['_locale'], $row['_current']);

        return $row;
    }

    protected function normalizeLocaleStoredValue(mixed $value): mixed
    {
        if (! is_array($value)) {
            return $value;
        }

        if (RichText::isEditorState($value)) {
            return RichText::toStoredString($value);
        }

        if (array_is_list($value)) {
            return array_values(array_filter($value, fn ($item) => filled($item)));
        }

        return RichText::toStoredString($value);
    }
}
