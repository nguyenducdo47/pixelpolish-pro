<?php

/**
 * Content profile definitions (Phase E — config-driven).
 *
 * @see docs/business/verticals.md
 */
return [
    'default_demo_slug' => 'nguyenducdo',

    'demo_slugs' => [
        'it' => 'nguyenducdo',
        'general' => 'demo-general',
        'creative' => 'demo-creative',
        'education' => 'demo-education',
        'business' => 'demo-business',
    ],

    'wizard_step_order' => [
        'profile',
        'skills',
        'projects',
        'background',
        'presence',
        'cv',
        'preview',
    ],

    'profiles' => [
        'it' => [
            'skill_display' => 'percent',
            'show_tech_logos' => true,
            'rich_cv_projects' => true,
            'suggested_theme' => 'midnight',
            'sections' => [
                'skills' => true,
                'projects' => true,
                'philosophy' => true,
            ],
            'project_fields' => [
                'complexity' => true,
                'problem' => true,
                'solution' => true,
                'learned' => true,
                'tech_stack' => true,
                'github_url' => true,
                'highlights' => true,
                'summary' => true,
                'demo_url' => true,
            ],
        ],
        'general' => [
            'skill_display' => 'list',
            'show_tech_logos' => false,
            'rich_cv_projects' => true,
            'suggested_theme' => 'aurora',
            'sections' => [
                'skills' => true,
                'projects' => true,
                'philosophy' => true,
            ],
            'project_fields' => [
                'complexity' => false,
                'problem' => false,
                'solution' => false,
                'learned' => false,
                'tech_stack' => false,
                'github_url' => false,
                'highlights' => true,
                'summary' => true,
                'demo_url' => true,
            ],
        ],
        'creative' => [
            'skill_display' => 'list',
            'show_tech_logos' => false,
            'rich_cv_projects' => true,
            'suggested_theme' => 'aurora',
            'sections' => [
                'skills' => true,
                'projects' => true,
                'philosophy' => true,
            ],
            'project_fields' => [
                'complexity' => false,
                'problem' => false,
                'solution' => false,
                'learned' => false,
                'tech_stack' => true,
                'github_url' => false,
                'highlights' => true,
                'summary' => true,
                'demo_url' => true,
            ],
        ],
        'education' => [
            'skill_display' => 'list',
            'show_tech_logos' => false,
            'rich_cv_projects' => true,
            'suggested_theme' => 'aurora',
            'sections' => [
                'skills' => true,
                'projects' => true,
                'philosophy' => true,
            ],
            'project_fields' => [
                'complexity' => false,
                'problem' => false,
                'solution' => false,
                'learned' => true,
                'tech_stack' => false,
                'github_url' => false,
                'highlights' => true,
                'summary' => true,
                'demo_url' => true,
            ],
        ],
        'business' => [
            'skill_display' => 'list',
            'show_tech_logos' => false,
            'rich_cv_projects' => true,
            'suggested_theme' => 'midnight',
            'wizard_steps' => [
                'profile',
                'projects',
                'skills',
                'background',
                'presence',
                'cv',
                'preview',
            ],
            'sections' => [
                'skills' => true,
                'projects' => true,
                'philosophy' => false,
            ],
            'project_fields' => [
                'complexity' => false,
                'problem' => true,
                'solution' => true,
                'learned' => false,
                'tech_stack' => false,
                'github_url' => false,
                'highlights' => true,
                'summary' => true,
                'demo_url' => true,
            ],
        ],
    ],
];
