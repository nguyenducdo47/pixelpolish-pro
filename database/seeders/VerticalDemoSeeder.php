<?php

namespace Database\Seeders;

use App\Enums\ContentProfile;
use App\Models\User;
use App\Support\AppearanceTheme;
use App\Support\ContentProfileConfig;
use App\Support\ContentProfileDemoSamples;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class VerticalDemoSeeder extends Seeder
{
    public function run(): void
    {
        $slugs = config('content_profiles.demo_slugs', []);

        foreach (ContentProfile::cases() as $profile) {
            $slug = $slugs[$profile->value] ?? null;

            if (! is_string($slug) || $slug === '' || $slug === 'nguyenducdo') {
                continue;
            }

            $this->seedDemo($profile, $slug);
        }
    }

    protected function seedDemo(ContentProfile $profile, string $slug): void
    {
        $sample = ContentProfileDemoSamples::portfolio($profile);
        $userMeta = $sample['user'] ?? ['name' => 'Demo User', 'email' => "demo-{$profile->value}@portfotilo.local"];

        $user = User::query()->updateOrCreate(
            ['email' => $userMeta['email']],
            [
                'name' => $userMeta['name'],
                'username' => $slug,
                'password' => 'password',
                'is_admin' => false,
            ]
        );

        $portfolio = $user->portfolio()->firstOrCreate(
            ['user_id' => $user->id],
            ['slug' => $slug]
        );

        $portfolio->update([
            'slug' => $slug,
            'content_profile' => $profile,
            'is_published' => true,
            'default_locale' => 'vi',
            'default_theme' => 'system',
            'seo_title' => $userMeta['name'].' | Portfotilo demo',
        ]);

        $portfolio->profile()->updateOrCreate(
            ['portfolio_id' => $portfolio->id],
            [
                'full_name' => $userMeta['name'],
                'email' => $userMeta['email'],
                'headline' => $sample['headline'] ?? [],
                'tagline' => $sample['tagline'] ?? [],
                'about' => $sample['about'] ?? [],
            ]
        );

        $portfolio->cvSettings()->updateOrCreate(
            ['portfolio_id' => $portfolio->id],
            [
                'template' => 'modern',
                'show_principles' => ContentProfileConfig::forProfile($profile)->sectionEnabled('philosophy'),
            ]
        );

        ContentProfileConfig::forProfile($profile)->applySuggestedThemeIfUnset($portfolio->fresh());

        $this->seedSkills($portfolio, $sample['skill_categories'] ?? []);
        $this->seedProjects($portfolio, $sample['projects'] ?? [], $profile);
    }

    /**
     * @param  array<int, array<string, mixed>>  $categories
     */
    protected function seedSkills(mixed $portfolio, array $categories): void
    {
        $portfolio->skillCategories()->delete();

        foreach ($categories as $index => $category) {
            $created = $portfolio->skillCategories()->create([
                'name' => $category['name'] ?? [],
                'sort_order' => $index,
            ]);

            foreach ($category['skills'] ?? [] as $skillIndex => $skill) {
                $created->skills()->create([
                    'name' => $skill['name'] ?? 'Skill',
                    'description' => $skill['description'] ?? [],
                    'level' => (int) ($skill['level'] ?? 70),
                    'sort_order' => $skillIndex,
                ]);
            }
        }
    }

    /**
     * @param  array<int, array<string, mixed>>  $projects
     */
    protected function seedProjects(mixed $portfolio, array $projects, ContentProfile $profile): void
    {
        $portfolio->projects()->delete();
        $config = ContentProfileConfig::forProfile($profile);

        foreach ($projects as $index => $row) {
            $payload = [
                'title' => $row['title'] ?? ['vi' => 'Demo', 'en' => 'Demo'],
                'subtitle' => $row['subtitle'] ?? [],
                'summary' => $row['summary'] ?? [],
                'highlights' => $row['highlights'] ?? [],
                'period' => $row['period'] ?? null,
                'sort_order' => $index,
            ];

            if ($config->showsProjectField('problem')) {
                $payload['problem'] = $row['problem'] ?? [];
            }

            if ($config->showsProjectField('solution')) {
                $payload['solution'] = $row['solution'] ?? [];
            }

            if ($config->showsProjectField('learned')) {
                $payload['learned'] = $row['learned'] ?? [];
            }

            if ($config->showsProjectField('tech_stack')) {
                $payload['tech_stack'] = $row['tech_stack'] ?? [];
            }

            $portfolio->projects()->create($payload);
        }
    }
}
