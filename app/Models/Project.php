<?php

namespace App\Models;

use App\Support\HasLocaleText;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Project extends Model
{
    use Concerns\BelongsToPortfolio;
    use HasLocaleText;

    protected $fillable = [
        'portfolio_id',
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
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'title' => 'array',
            'subtitle' => 'array',
            'complexity' => 'array',
            'summary' => 'array',
            'problem' => 'array',
            'solution' => 'array',
            'learned' => 'array',
            'highlights' => 'array',
            'tech_stack' => 'array',
            'is_featured' => 'boolean',
        ];
    }

    public function portfolio(): BelongsTo
    {
        return $this->belongsTo(Portfolio::class);
    }
}
