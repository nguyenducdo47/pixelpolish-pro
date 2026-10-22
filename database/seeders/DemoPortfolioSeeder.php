<?php

namespace Database\Seeders;

use App\Enums\ContentProfile;
use App\Models\User;
use App\Support\ContentProfileConfig;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DemoPortfolioSeeder extends Seeder
{
    public function run(): void
    {
        $vi = json_decode(file_get_contents(base_path('_legacy-react/src/i18n/vi.json')), true);
        $en = json_decode(file_get_contents(base_path('_legacy-react/src/i18n/en.json')), true);
        $cvOverlay = require database_path('seeders/data/duc_do_logistics_digital_cv.php');

        $user = User::query()->updateOrCreate(
            ['email' => 'ducdonguyen.dev@gmail.com'],
            [
                'name' => 'Nguyễn Đức Độ',
                'username' => 'nguyenducdo',
                'password' => 'password',
                'is_admin' => true,
            ]
        );

        $portfolio = $user->portfolio()->firstOrCreate(
            ['user_id' => $user->id],
            ['slug' => 'nguyenducdo']
        );

        $portfolio->update([
            'slug' => 'nguyenducdo',
            'content_profile' => ContentProfile::Business,
            'is_published' => true,
            'default_locale' => 'vi',
            'default_theme' => 'system',
            'seo_title' => 'Nguyễn Đức Độ | Kho vận & Nâng cấp hệ thống quản lý kho',
            'seo_description' => $cvOverlay['profile']['tagline']['vi'] ?? ($vi['hero']['description'] ?? null),
        ]);

        $dateOfBirth = null;
        if (! empty($vi['resume']['dob'])) {
            try {
                $dateOfBirth = Carbon::createFromFormat('d/m/Y', $vi['resume']['dob']);
            } catch (\Throwable $e) {
                $dateOfBirth = null;
            }
        }

        $portfolio->profile()->updateOrCreate(
            ['portfolio_id' => $portfolio->id],
            [
                'full_name' => 'Nguyễn Đức Độ',
                'email' => 'ducdonguyen.dev@gmail.com',
                'phone' => '0879292571',
                'date_of_birth' => $dateOfBirth,
                'headline' => $cvOverlay['profile']['headline'],
                'tagline' => $cvOverlay['profile']['tagline'],
                'about' => $cvOverlay['profile']['about'],
                'philosophy_quote' => $cvOverlay['profile']['philosophy_quote'] ?? [
                    'vi' => strip_tags(str_replace(['<highlight>', '</highlight>'], '', $vi['philosophy']['quote'] ?? '')),
                    'en' => strip_tags(str_replace(['<highlight>', '</highlight>'], '', $en['philosophy']['quote'] ?? '')),
                ],
            ]
        );

        $portfolio->cvSettings()->updateOrCreate(
            ['portfolio_id' => $portfolio->id],
            [
                'template' => 'modern',
                ...($cvOverlay['cv_settings'] ?? []),
            ]
        );

        $portfolio->socialLinks()->delete();
        foreach ($vi['cta']['socialLinks'] ?? [] as $platform => $link) {
            $portfolio->socialLinks()->create([
                'platform' => strtolower($platform) === 'github' ? 'github' : strtolower($platform),
                'url' => $link['url'],
            ]);
        }

        $portfolio->skillCategories()->delete();
        $logisticsCategoryOffset = count($cvOverlay['skill_categories'] ?? []);
        foreach ($cvOverlay['skill_categories'] ?? [] as $category) {
            $created = $portfolio->skillCategories()->create([
                'name' => $category['name'],
                'sort_order' => $category['sort_order'],
            ]);
            foreach ($category['skills'] ?? [] as $skillIndex => $skill) {
                $created->skills()->create([
                    'name' => $skill['name'],
                    'description' => $skill['description'],
                    'level' => $skill['level'] ?? 50,
                    'sort_order' => $skillIndex,
                ]);
            }
        }
        if (! ($cvOverlay['skip_legacy_skill_categories'] ?? false)) {
            foreach ($vi['skills']['categories'] ?? [] as $index => $category) {
                $enCategory = $en['skills']['categories'][$index] ?? $category;
                $created = $portfolio->skillCategories()->create([
                    'name' => ['vi' => $category['title'], 'en' => $enCategory['title']],
                    'sort_order' => $index + $logisticsCategoryOffset,
                ]);
                foreach ($category['items'] ?? [] as $skillIndex => $skill) {
                    $enSkill = $enCategory['items'][$skillIndex] ?? $skill;
                    $created->skills()->create([
                        'name' => $skill['name'],
                        'description' => ['vi' => $skill['description'] ?? '', 'en' => $enSkill['description'] ?? ''],
                        'level' => $skill['level'] ?? 50,
                        'sort_order' => $skillIndex,
                    ]);
                }
            }
        }

        $portfolio->projects()->delete();
        $overlayProjects = ($cvOverlay['replace_legacy_projects'] ?? false)
            ? ($cvOverlay['projects'] ?? [])
            : null;
        if ($overlayProjects !== null) {
            foreach ($overlayProjects as $index => $project) {
                $portfolio->projects()->create($this->mapOverlayProject($project, $index));
            }
        } else {
            $projectPatches = $cvOverlay['project_patches'] ?? [];
            $legacyProjects = $vi['projects']['items'] ?? [];
            $prependProjects = $cvOverlay['prepend_projects'] ?? [];
            $allProjects = array_merge($prependProjects, $legacyProjects);
            foreach ($allProjects as $index => $project) {
                $isPrepended = $index < count($prependProjects);
                $legacyIndex = $index - count($prependProjects);
                $enProject = $isPrepended
                    ? $project
                    : ($en['projects']['items'][$legacyIndex] ?? $project);

                $titleVi = $isPrepended ? ($project['title']['vi'] ?? '') : ($project['title'] ?? '');
                $patch = null;
                foreach ($projectPatches as $needle => $patchData) {
                    if (str_contains($titleVi, $needle)) {
                        $patch = $patchData;
                        break;
                    }
                }

                $portfolio->projects()->create([
                    'title' => $isPrepended
                        ? $project['title']
                        : ['vi' => $project['title'], 'en' => $enProject['title']],
                    'subtitle' => $patch['subtitle'] ?? ($isPrepended
                        ? ($project['subtitle'] ?? ['vi' => '', 'en' => ''])
                        : ['vi' => $project['subtitle'] ?? '', 'en' => $enProject['subtitle'] ?? '']),
                    'complexity' => $isPrepended
                        ? ['vi' => '', 'en' => '']
                        : ['vi' => $project['complexity'] ?? '', 'en' => $enProject['complexity'] ?? ''],
                    'summary' => $patch['solution'] ?? ($isPrepended
                        ? ($project['solution'] ?? ['vi' => '', 'en' => ''])
                        : ['vi' => $project['solution'] ?? '', 'en' => $enProject['solution'] ?? '']),
                    'problem' => $isPrepended
                        ? ['vi' => '', 'en' => '']
                        : ['vi' => $project['problem'] ?? '', 'en' => $enProject['problem'] ?? ''],
                    'solution' => $patch['solution'] ?? ($isPrepended
                        ? ($project['solution'] ?? ['vi' => '', 'en' => ''])
                        : ['vi' => $project['solution'] ?? '', 'en' => $enProject['solution'] ?? '']),
                    'learned' => $isPrepended
                        ? ['vi' => '', 'en' => '']
                        : ['vi' => $project['learned'] ?? '', 'en' => $enProject['learned'] ?? ''],
                    'highlights' => $patch['highlights'] ?? ($isPrepended
                        ? ($project['highlights'] ?? ['vi' => [], 'en' => []])
                        : [
                            'vi' => $project['responsibilities'] ?? [],
                            'en' => $enProject['responsibilities'] ?? [],
                        ]),
                    'tech_stack' => $patch['tech_stack'] ?? ($isPrepended
                        ? ($project['tech_stack'] ?? [])
                        : ($project['techStack'] ?? [])),
                    'period' => $project['period'] ?? null,
                    'demo_url' => $isPrepended ? null : ($project['demoUrl'] ?? null),
                    'demo_label' => $isPrepended ? null : ($project['demoLabel'] ?? null),
                    'sort_order' => $index,
                ]);
            }
        }

        $portfolio->education()->delete();
        foreach ($vi['resume']['education']['items'] ?? [] as $index => $item) {
            $enItem = $en['resume']['education']['items'][$index] ?? $item;
            $portfolio->education()->create([
                'degree' => ['vi' => $item['degree'], 'en' => $enItem['degree']],
                'school' => ['vi' => $item['school'], 'en' => $enItem['school']],
                'details' => ['vi' => $item['details'] ?? '', 'en' => $enItem['details'] ?? ''],
                'period' => $item['period'] ?? null,
                'sort_order' => $index,
            ]);
        }

        $portfolio->spokenLanguages()->delete();
        foreach ($vi['resume']['languages'] ?? [] as $index => $item) {
            $enItem = $en['resume']['languages'][$index] ?? $item;
            $portfolio->spokenLanguages()->create([
                'name' => ['vi' => $item['name'], 'en' => $enItem['name']],
                'level' => ['vi' => $item['level'], 'en' => $enItem['level']],
                'sort_order' => $index,
            ]);
        }

        $portfolio->principles()->delete();
        $principleIndex = 0;
        if (! ($cvOverlay['skip_legacy_principles'] ?? false)) {
            foreach ($vi['philosophy']['principles'] ?? [] as $item) {
                $enItem = $en['philosophy']['principles'][$principleIndex] ?? $item;
                $portfolio->principles()->create([
                    'title' => ['vi' => $item['title'], 'en' => $enItem['title']],
                    'description' => ['vi' => $item['description'], 'en' => $enItem['description']],
                    'sort_order' => $principleIndex,
                ]);
                $principleIndex++;
            }
        }
        foreach ($cvOverlay['extra_principles'] ?? [] as $item) {
            $portfolio->principles()->create([
                'title' => $item['title'],
                'description' => $item['description'],
                'sort_order' => $principleIndex,
            ]);
            $principleIndex++;
        }

        $portfolio->cvListItems()->delete();
        foreach ($cvOverlay['cv_list_items'] ?? [] as $type => $items) {
            foreach ($items as $index => $item) {
                $portfolio->cvListItems()->create([
                    'type' => $type,
                    'title' => $item['title'],
                    'sort_order' => $index,
                ]);
            }
        }

        ContentProfileConfig::forProfile(ContentProfile::Business)->applySuggestedThemeIfUnset($portfolio->fresh());
    }

    /**
     * @param  array<string, mixed>  $project
     * @return array<string, mixed>
     */
    private function mapOverlayProject(array $project, int $sortOrder): array
    {
        $solution = $project['solution'] ?? ['vi' => '', 'en' => ''];

        return [
            'title' => $project['title'],
            'subtitle' => $project['subtitle'] ?? ['vi' => '', 'en' => ''],
            'complexity' => ['vi' => '', 'en' => ''],
            'summary' => $solution,
            'problem' => ['vi' => '', 'en' => ''],
            'solution' => $solution,
            'learned' => ['vi' => '', 'en' => ''],
            'highlights' => $project['highlights'] ?? ['vi' => [], 'en' => []],
            'tech_stack' => $project['tech_stack'] ?? [],
            'period' => $project['period'] ?? null,
            'demo_url' => $project['demo_url'] ?? null,
            'demo_label' => $project['demo_label'] ?? null,
            'sort_order' => $sortOrder,
        ];
    }
}
