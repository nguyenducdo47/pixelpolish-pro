<?php

namespace Database\Seeders;

use App\Enums\ContentProfile;
use App\Models\Cv;
use App\Models\Portfolio;
use App\Models\User;
use App\Support\ContentProfileConfig;
use Database\Seeders\Concerns\SeedsCvStrengthListItems;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DemoPortfolioSeeder extends Seeder
{
    use SeedsCvStrengthListItems;

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
            'seo_title' => 'Nguyễn Đức Độ | Portfolio',
            'seo_description' => $vi['hero']['description'] ?? null,
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
                'full_name' => [
                    'vi' => 'Nguyễn Đức Độ',
                    'en' => 'Nguyen Duc Do',
                ],
                'email' => 'ducdonguyen.dev@gmail.com',
                'phone' => '0899068281',
                'date_of_birth' => $dateOfBirth,
                'headline' => [
                    'vi' => $vi['resume']['experience']['position'] ?? 'PHP Laravel Developer',
                    'en' => $en['resume']['experience']['position'] ?? 'PHP Laravel Developer',
                ],
                'tagline' => [
                    'vi' => $vi['hero']['description'] ?? '',
                    'en' => $en['hero']['description'] ?? '',
                ],
                'about' => [
                    'vi' => '<p>'.nl2br(e($vi['resume']['summary'] ?? ''), false).'</p>',
                    'en' => '<p>'.nl2br(e($en['resume']['summary'] ?? ''), false).'</p>',
                ],
                'philosophy_quote' => [
                    'vi' => strip_tags(str_replace(['<highlight>', '</highlight>'], '', $vi['philosophy']['quote'] ?? '')),
                    'en' => strip_tags(str_replace(['<highlight>', '</highlight>'], '', $en['philosophy']['quote'] ?? '')),
                ],
            ]
        );

        $defaultCv = $portfolio->cvs()->updateOrCreate(
            ['portfolio_id' => $portfolio->id, 'slug' => 'default'],
            [
                'name' => ['vi' => 'CV mặc định', 'en' => 'Default CV'],
                'is_default' => true,
                'show_strengths' => true,
                'template' => 'modern',
            ]
        );

        $this->seedCvStrengthsFromAboutHighlights($portfolio, $defaultCv, $vi, $en);

        $portfolio->socialLinks()->delete();
        foreach ($vi['cta']['socialLinks'] ?? [] as $platform => $link) {
            $portfolio->socialLinks()->create([
                'platform' => strtolower($platform) === 'github' ? 'github' : strtolower($platform),
                'url' => $link['url'],
            ]);
        }

        $this->purgePortfolioSiteContent($portfolio);
        $this->seedLegacyPortfolioContent($portfolio, $vi, $en);

        $logisticsCv = $portfolio->cvs()->updateOrCreate(
            ['portfolio_id' => $portfolio->id, 'slug' => $cvOverlay['slug']],
            [
                'name' => $cvOverlay['name'],
                'is_default' => (bool) ($cvOverlay['is_default'] ?? false),
                'headline' => $cvOverlay['profile']['headline'],
                'tagline' => $cvOverlay['profile']['tagline'],
                'about' => $cvOverlay['profile']['about'],
                'philosophy_quote' => $cvOverlay['profile']['philosophy_quote'] ?? null,
                'template' => 'modern',
                ...($cvOverlay['cv_settings'] ?? []),
            ]
        );

        $this->purgeCvDocumentContent($portfolio, $logisticsCv);
        $this->seedCvDocumentFromOverlay($portfolio, $logisticsCv, $cvOverlay);

        ContentProfileConfig::forProfile(ContentProfile::Business)->applySuggestedThemeIfUnset($portfolio->fresh());
    }

    private function purgePortfolioSiteContent(Portfolio $portfolio): void
    {
        $portfolio->skillCategories()->whereNull('cv_id')->delete();
        $portfolio->projects()->whereNull('cv_id')->delete();
        $portfolio->principles()->whereNull('cv_id')->delete();
    }

    private function purgeCvDocumentContent(Portfolio $portfolio, Cv $cv): void
    {
        $portfolio->skillCategories()->where('cv_id', $cv->id)->delete();
        $portfolio->projects()->where('cv_id', $cv->id)->delete();
        $portfolio->principles()->where('cv_id', $cv->id)->delete();
        $portfolio->workExperiences()->where('cv_id', $cv->id)->delete();
        $portfolio->cvListItems()->where('cv_id', $cv->id)->delete();
    }

    /**
     * @param  array<string, mixed>  $vi
     * @param  array<string, mixed>  $en
     */
    private function seedLegacyPortfolioContent(Portfolio $portfolio, array $vi, array $en): void
    {
        foreach ($vi['skills']['categories'] ?? [] as $index => $category) {
            $enCategory = $en['skills']['categories'][$index] ?? $category;
            $created = $portfolio->skillCategories()->create([
                'cv_id' => null,
                'name' => ['vi' => $category['title'], 'en' => $enCategory['title']],
                'sort_order' => $index,
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

        foreach ($vi['projects']['items'] ?? [] as $index => $project) {
            $enProject = $en['projects']['items'][$index] ?? $project;
            $portfolio->projects()->create([
                'cv_id' => null,
                'title' => ['vi' => $project['title'], 'en' => $enProject['title']],
                'subtitle' => ['vi' => $project['subtitle'] ?? '', 'en' => $enProject['subtitle'] ?? ''],
                'complexity' => ['vi' => $project['complexity'] ?? '', 'en' => $enProject['complexity'] ?? ''],
                'summary' => ['vi' => $project['solution'] ?? '', 'en' => $enProject['solution'] ?? ''],
                'problem' => ['vi' => $project['problem'] ?? '', 'en' => $enProject['problem'] ?? ''],
                'solution' => ['vi' => $project['solution'] ?? '', 'en' => $enProject['solution'] ?? ''],
                'learned' => ['vi' => $project['learned'] ?? '', 'en' => $enProject['learned'] ?? ''],
                'highlights' => [
                    'vi' => $project['responsibilities'] ?? [],
                    'en' => $enProject['responsibilities'] ?? [],
                ],
                'tech_stack' => $project['techStack'] ?? [],
                'period' => $project['period'] ?? null,
                'demo_url' => $project['demoUrl'] ?? null,
                'demo_label' => $project['demoLabel'] ?? null,
                'sort_order' => $index,
            ]);
        }

        $principleIndex = 0;
        foreach ($vi['philosophy']['principles'] ?? [] as $item) {
            $enItem = $en['philosophy']['principles'][$principleIndex] ?? $item;
            $portfolio->principles()->create([
                'cv_id' => null,
                'title' => ['vi' => $item['title'], 'en' => $enItem['title']],
                'description' => ['vi' => $item['description'], 'en' => $enItem['description']],
                'sort_order' => $principleIndex,
            ]);
            $principleIndex++;
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
    }

    /**
     * @param  array<string, mixed>  $overlay
     */
    private function seedCvDocumentFromOverlay(Portfolio $portfolio, Cv $cv, array $overlay): void
    {
        foreach ($overlay['skill_categories'] ?? [] as $category) {
            $created = $portfolio->skillCategories()->create([
                'cv_id' => $cv->id,
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

        foreach ($overlay['projects'] ?? [] as $index => $project) {
            $portfolio->projects()->create([
                ...$this->mapOverlayProject($project, $index),
                'cv_id' => $cv->id,
            ]);
        }

        foreach ($overlay['work_experiences'] ?? [] as $index => $item) {
            $portfolio->workExperiences()->create([
                'cv_id' => $cv->id,
                'company' => $item['company'],
                'position' => $item['position'],
                'details' => $item['details'] ?? ['vi' => '', 'en' => ''],
                'period' => $item['period'] ?? null,
                'sort_order' => $index,
            ]);
        }

        foreach ($overlay['extra_principles'] ?? [] as $index => $item) {
            $portfolio->principles()->create([
                'cv_id' => $cv->id,
                'title' => $item['title'],
                'description' => $item['description'],
                'sort_order' => $index,
            ]);
        }

        foreach ($overlay['cv_list_items'] ?? [] as $type => $items) {
            foreach ($items as $index => $item) {
                $portfolio->cvListItems()->create([
                    'cv_id' => $cv->id,
                    'type' => $type,
                    'title' => $item['title'],
                    'sort_order' => $index,
                ]);
            }
        }
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
