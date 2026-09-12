<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CvSetting extends Model
{
    use Concerns\BelongsToPortfolio;

    protected $fillable = [
        'portfolio_id',
        'template',
        'show_avatar',
        'show_about',
        'show_skills',
        'show_projects',
        'show_education',
        'show_languages',
        'show_principles',
    ];

    protected function casts(): array
    {
        return [
            'show_avatar' => 'boolean',
            'show_about' => 'boolean',
            'show_skills' => 'boolean',
            'show_projects' => 'boolean',
            'show_education' => 'boolean',
            'show_languages' => 'boolean',
            'show_principles' => 'boolean',
        ];
    }

    public function portfolio(): BelongsTo
    {
        return $this->belongsTo(Portfolio::class);
    }
}
