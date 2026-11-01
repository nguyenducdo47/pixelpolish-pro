<?php

namespace Database\Seeders;

use App\Models\Cv;
use App\Models\Portfolio;
use App\Models\User;
use Database\Seeders\Concerns\SeedsCvStrengthListItems;
use Illuminate\Database\Seeder;

class DemoItCvSeeder extends Seeder
{
    use SeedsCvStrengthListItems;

    public function run(): void
    {
        $portfolio = User::query()
            ->where('email', 'ducdonguyen.dev@gmail.com')
            ->firstOrFail()
            ->portfolio;

        $vi = json_decode(file_get_contents(base_path('_legacy-react/src/i18n/vi.json')), true);
        $en = json_decode(file_get_contents(base_path('_legacy-react/src/i18n/en.json')), true);
        $meta = require database_path('seeders/data/duc_do_it_cv.php');

        $itCv = $portfolio->cvs()->updateOrCreate(
            ['portfolio_id' => $portfolio->id, 'slug' => $meta['slug']],
            [
                'name' => $meta['name'],
                'is_default' => (bool) ($meta['is_default'] ?? false),
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
                'template' => 'modern',
                ...($meta['cv_settings'] ?? []),
            ]
        );

        if ($itCv->is_default) {
            $portfolio->cvs()->where('id', '!=', $itCv->id)->update(['is_default' => false]);
        }

        $this->purgeCvDocumentContent($portfolio, $itCv);
        $this->seedLegacyI18nCvContent($portfolio, $itCv->id, $vi, $en);
        $this->seedItCvExtrasFromLegacy($portfolio, $itCv, $vi, $en);
        $this->seedCvStrengthsFromAboutHighlights($portfolio, $itCv, $vi, $en);
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
    private function seedLegacyI18nCvContent(Portfolio $portfolio, int $cvId, array $vi, array $en): void
    {
        foreach ($vi['skills']['categories'] ?? [] as $index => $category) {
            $enCategory = $en['skills']['categories'][$index] ?? $category;
            $created = $portfolio->skillCategories()->create([
                'cv_id' => $cvId,
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
                'cv_id' => $cvId,
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
                'cv_id' => $cvId,
                'title' => ['vi' => $item['title'], 'en' => $enItem['title']],
                'description' => ['vi' => $item['description'], 'en' => $enItem['description']],
                'sort_order' => $principleIndex,
            ]);
            $principleIndex++;
        }
    }

    /**
     * @param  array<string, mixed>  $vi
     * @param  array<string, mixed>  $en
     */
    private function seedItCvExtrasFromLegacy(Portfolio $portfolio, Cv $cv, array $vi, array $en): void
    {
        $experience = $vi['resume']['experience'] ?? [];
        $experienceEn = $en['resume']['experience'] ?? [];

        if ($experience !== []) {
            $portfolio->workExperiences()->create([
                'cv_id' => $cv->id,
                'company' => [
                    'vi' => $experience['company'] ?? '',
                    'en' => $experienceEn['company'] ?? '',
                ],
                'position' => [
                    'vi' => $experience['position'] ?? '',
                    'en' => $experienceEn['position'] ?? '',
                ],
                'period' => $experience['period'] ?? null,
                'details' => $this->resolveResumeEmploymentDetails($experience, $experienceEn),
                'sort_order' => 0,
            ]);
        }
    }

    /**
     * @param  array<string, mixed>  $vi
     * @param  array<string, mixed>  $en
     * @return array{vi: string, en: string}
     */
    private function resolveResumeEmploymentDetails(array $vi, array $en): array
    {
        $viDetails = $vi['details'] ?? null;
        $enDetails = $en['details'] ?? null;

        if (is_array($viDetails) || is_array($enDetails)) {
            return [
                'vi' => is_array($viDetails) ? (string) ($viDetails['vi'] ?? '') : (string) $viDetails,
                'en' => is_array($enDetails) ? (string) ($enDetails['en'] ?? '') : (string) $enDetails,
            ];
        }

        return [
            'vi' => is_string($viDetails) ? $viDetails : '',
            'en' => is_string($enDetails) ? $enDetails : '',
        ];
    }
}
