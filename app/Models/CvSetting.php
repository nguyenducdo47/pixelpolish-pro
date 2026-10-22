<?php

namespace App\Models;

use App\Support\HasLocaleText;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CvSetting extends Model
{
    use Concerns\BelongsToPortfolio;
    use HasLocaleText;

    protected $fillable = [
        'portfolio_id',
        'template',
        'resume_summary_title',
        'resume_summary',
        'show_resume_summary',
        'show_avatar',
        'show_about',
        'show_skills',
        'show_projects',
        'show_project_period',
        'show_social_links',
        'show_certifications',
        'show_career_targets',
        'show_strengths',
        'show_education',
        'show_work_experience',
        'show_languages',
        'show_principles',
    ];

    protected function casts(): array
    {
        return [
            'resume_summary_title' => 'array',
            'resume_summary' => 'array',
            'show_resume_summary' => 'boolean',
            'show_avatar' => 'boolean',
            'show_about' => 'boolean',
            'show_skills' => 'boolean',
            'show_projects' => 'boolean',
            'show_project_period' => 'boolean',
            'show_social_links' => 'boolean',
            'show_certifications' => 'boolean',
            'show_career_targets' => 'boolean',
            'show_strengths' => 'boolean',
            'show_education' => 'boolean',
            'show_work_experience' => 'boolean',
            'show_languages' => 'boolean',
            'show_principles' => 'boolean',
        ];
    }

    public function portfolio(): BelongsTo
    {
        return $this->belongsTo(Portfolio::class);
    }
}
