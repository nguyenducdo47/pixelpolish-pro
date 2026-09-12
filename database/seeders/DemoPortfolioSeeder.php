<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DemoPortfolioSeeder extends Seeder
{
    public function run(): void
    {
        $vi = json_decode(file_get_contents(base_path('_legacy-react/src/i18n/vi.json')), true);
        $en = json_decode(file_get_contents(base_path('_legacy-react/src/i18n/en.json')), true);

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
            'is_published' => true,
            'default_locale' => 'vi',
            'default_theme' => 'system',
            'seo_title' => 'Nguyễn Đức Độ | Web Developer Laravel',
            'seo_description' => $vi['hero']['description'] ?? null,
        ]);

        $portfolio->profile()->updateOrCreate(
            ['portfolio_id' => $portfolio->id],
            [
                'full_name' => 'Nguyễn Đức Độ',
                'email' => 'ducdonguyen.dev@gmail.com',
                'phone' => '0879292571',
                'headline' => [
                    'vi' => $vi['hero']['badge'] ?? 'Web Developer',
                    'en' => $en['hero']['badge'] ?? 'Web Developer',
                ],
                'tagline' => [
                    'vi' => $vi['hero']['description'] ?? '',
                    'en' => $en['hero']['description'] ?? '',
                ],
                'about' => [
                    'vi' => trim(($vi['about']['intro1'] ?? '').' '.($vi['about']['laravelHighlight'] ?? '').' '.($vi['about']['intro1End'] ?? '')."\n\n".($vi['about']['intro2'] ?? '')."\n\n".($vi['about']['intro3'] ?? '')),
                    'en' => trim(($en['about']['intro1'] ?? '').' '.($en['about']['laravelHighlight'] ?? '').' '.($en['about']['intro1End'] ?? '')."\n\n".($en['about']['intro2'] ?? '')."\n\n".($en['about']['intro3'] ?? '')),
                ],
                'philosophy_quote' => [
                    'vi' => strip_tags(str_replace(['<highlight>', '</highlight>'], '', $vi['philosophy']['quote'] ?? '')),
                    'en' => strip_tags(str_replace(['<highlight>', '</highlight>'], '', $en['philosophy']['quote'] ?? '')),
                ],
            ]
        );

        $portfolio->cvSettings()->updateOrCreate(
            ['portfolio_id' => $portfolio->id],
            ['template' => 'modern']
        );

        $portfolio->socialLinks()->delete();
        foreach ($vi['cta']['socialLinks'] ?? [] as $platform => $link) {
            $portfolio->socialLinks()->create([
                'platform' => strtolower($platform) === 'github' ? 'github' : strtolower($platform),
                'url' => $link['url'],
            ]);
        }

        $portfolio->skillCategories()->delete();
        foreach ($vi['skills']['categories'] ?? [] as $index => $category) {
            $enCategory = $en['skills']['categories'][$index] ?? $category;
            $created = $portfolio->skillCategories()->create([
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

        $portfolio->projects()->delete();
        foreach ($vi['projects']['items'] ?? [] as $index => $project) {
            $enProject = $en['projects']['items'][$index] ?? $project;
            $portfolio->projects()->create([
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
        foreach ($vi['philosophy']['principles'] ?? [] as $index => $item) {
            $enItem = $en['philosophy']['principles'][$index] ?? $item;
            $portfolio->principles()->create([
                'title' => ['vi' => $item['title'], 'en' => $enItem['title']],
                'description' => ['vi' => $item['description'], 'en' => $enItem['description']],
                'sort_order' => $index,
            ]);
        }
    }
}
