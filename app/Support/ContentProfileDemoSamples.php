<?php

namespace App\Support;

use App\Enums\ContentProfile;

/**
 * Minimal bilingual demo content for vertical showcase seeders.
 *
 * @return array<string, mixed>
 */
class ContentProfileDemoSamples
{
    public static function portfolio(ContentProfile $profile): array
    {
        return match ($profile) {
            ContentProfile::General => self::general(),
            ContentProfile::Creative => self::creative(),
            ContentProfile::Education => self::education(),
            ContentProfile::Business => self::business(),
            ContentProfile::It => self::it(),
        };
    }

    /**
     * @return array<string, mixed>
     */
    protected static function it(): array
    {
        return [
            'user' => ['name' => 'Nguyễn Đức Độ', 'email' => 'ducdonguyen.dev@gmail.com'],
            'headline' => ['vi' => 'Web Developer', 'en' => 'Web Developer'],
            'tagline' => [
                'vi' => 'Laravel, Vue và portfolio đa ngôn ngữ.',
                'en' => 'Laravel, Vue, and multilingual portfolios.',
            ],
            'about' => [
                'vi' => '<p>Demo IT — dùng làm mẫu cho developer.</p>',
                'en' => '<p>IT demo — template for developers.</p>',
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected static function general(): array
    {
        return [
            'user' => ['name' => 'Minh Anh', 'email' => 'demo-general@portfotilo.local'],
            'headline' => ['vi' => 'Chuyên viên vận hành', 'en' => 'Operations specialist'],
            'tagline' => [
                'vi' => 'Kinh nghiệm đa lĩnh vực, CV và web cùng một nguồn dữ liệu.',
                'en' => 'Cross-functional experience; one dataset for site and CV.',
            ],
            'about' => [
                'vi' => '<p>Portfolio mẫu cho mọi ngành nghề — không thuật ngữ IT.</p>',
                'en' => '<p>Sample portfolio for any profession — no IT jargon.</p>',
            ],
            'skill_categories' => [[
                'name' => ['vi' => 'Chuyên môn', 'en' => 'Expertise'],
                'skills' => [[
                    'name' => 'Project management',
                    'description' => ['vi' => 'Lập kế hoạch và theo dõi tiến độ.', 'en' => 'Planning and delivery tracking.'],
                    'level' => 80,
                ]],
            ]],
            'projects' => [[
                'title' => ['vi' => 'Quản lý dự án nội bộ', 'en' => 'Internal improvement project'],
                'subtitle' => ['vi' => 'Công ty dịch vụ', 'en' => 'Services company'],
                'summary' => ['vi' => '<p>Rút ngắn quy trình phê duyệt 30%.</p>', 'en' => '<p>Cut approval cycle by 30%.</p>'],
                'highlights' => ['vi' => ['Điều phối 4 phòng ban'], 'en' => ['Coordinated 4 departments']],
                'period' => '2023 – 2024',
            ]],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected static function creative(): array
    {
        return [
            'user' => ['name' => 'Lan Phương', 'email' => 'demo-creative@portfotilo.local'],
            'headline' => ['vi' => 'UI/UX Designer', 'en' => 'UI/UX Designer'],
            'tagline' => ['vi' => 'Thiết kế sản phẩm số và nhận diện thương hiệu.', 'en' => 'Digital product and brand design.'],
            'about' => ['vi' => '<p>Demo designer — tác phẩm và case study.</p>', 'en' => '<p>Designer demo — selected work and case studies.</p>'],
            'skill_categories' => [[
                'name' => ['vi' => 'Công cụ', 'en' => 'Tools'],
                'skills' => [[
                    'name' => 'Figma',
                    'description' => ['vi' => 'Design system & prototype.', 'en' => 'Design systems & prototypes.'],
                    'level' => 90,
                ]],
            ]],
            'projects' => [[
                'title' => ['vi' => 'App fintech', 'en' => 'Fintech app redesign'],
                'subtitle' => ['vi' => 'Case study', 'en' => 'Case study'],
                'summary' => ['vi' => '<p>Tăng completion onboarding 22%.</p>', 'en' => '<p>Raised onboarding completion by 22%.</p>'],
                'tech_stack' => ['Figma', 'Protopie'],
                'period' => '2024',
            ]],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected static function education(): array
    {
        return [
            'user' => ['name' => 'Thầy Hoàng', 'email' => 'demo-education@portfotilo.local'],
            'headline' => ['vi' => 'Giảng viên CNTT', 'en' => 'IT lecturer'],
            'tagline' => ['vi' => 'Đào tạo thực hành và hướng nghiệp.', 'en' => 'Hands-on training and career guidance.'],
            'about' => ['vi' => '<p>Demo giáo dục — khóa học và chương trình.</p>', 'en' => '<p>Education demo — courses and programs.</p>'],
            'skill_categories' => [[
                'name' => ['vi' => 'Lĩnh vực dạy', 'en' => 'Teaching areas'],
                'skills' => [[
                    'name' => 'Web fundamentals',
                    'description' => ['vi' => 'HTML, CSS, JS cho sinh viên năm 2.', 'en' => 'HTML, CSS, JS for sophomore students.'],
                    'level' => 85,
                ]],
            ]],
            'projects' => [[
                'title' => ['vi' => 'Khóa Laravel cơ bản', 'en' => 'Intro to Laravel'],
                'subtitle' => ['vi' => '40 giờ · 32 học viên', 'en' => '40h · 32 learners'],
                'summary' => ['vi' => '<p>100% hoàn thành project cuối khóa.</p>', 'en' => '<p>100% completed capstone projects.</p>'],
                'learned' => ['vi' => '<p>Sinh viên tự deploy portfolio.</p>', 'en' => '<p>Students deployed their own portfolios.</p>'],
                'period' => '2025',
            ]],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected static function business(): array
    {
        return [
            'user' => ['name' => 'Quốc Bảo', 'email' => 'demo-business@portfotilo.local'],
            'headline' => ['vi' => 'Tư vấn quy trình', 'en' => 'Process consultant'],
            'tagline' => ['vi' => 'Case study và kết quả đo lường được.', 'en' => 'Case studies with measurable outcomes.'],
            'about' => ['vi' => '<p>Demo kinh doanh — không mục triết lý trên site.</p>', 'en' => '<p>Business demo — no philosophy section on site.</p>'],
            'skill_categories' => [[
                'name' => ['vi' => 'Năng lực', 'en' => 'Competencies'],
                'skills' => [[
                    'name' => 'Stakeholder management',
                    'description' => ['vi' => 'Workshop và roadmap.', 'en' => 'Workshops and roadmaps.'],
                    'level' => 88,
                ]],
            ]],
            'projects' => [[
                'title' => ['vi' => 'Tối ưu CRM', 'en' => 'CRM optimization'],
                'subtitle' => ['vi' => 'Khách hàng B2B', 'en' => 'B2B client'],
                'problem' => ['vi' => '<p>Lead rơi rụng giữa marketing và sales.</p>', 'en' => '<p>Leads lost between marketing and sales.</p>'],
                'solution' => ['vi' => '<p>Giảm 18% thời gian chốt hợp đồng.</p>', 'en' => '<p>Reduced time-to-close by 18%.</p>'],
                'highlights' => ['vi' => ['Audit 6 tuần'], 'en' => ['6-week audit']],
                'period' => '2024',
            ]],
        ];
    }
}
