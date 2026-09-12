<?php

namespace App\Models;

use App\Support\HasLocaleText;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SkillCategory extends Model
{
    use Concerns\BelongsToPortfolio;
    use HasLocaleText;

    protected $fillable = [
        'portfolio_id',
        'name',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'name' => 'array',
        ];
    }

    public function portfolio(): BelongsTo
    {
        return $this->belongsTo(Portfolio::class);
    }

    public function skills(): HasMany
    {
        return $this->hasMany(Skill::class)->orderBy('sort_order');
    }
}
