<?php

namespace App\Models;

use App\Support\HasLocaleText;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Skill extends Model
{
    use HasLocaleText;

    protected static function booted(): void
    {
        static::addGlobalScope('current_portfolio', function (Builder $query): void {
            if (! Filament::isServing()) {
                return;
            }

            $portfolioId = auth()->user()?->portfolio?->id;
            if ($portfolioId) {
                $query->whereHas('category', fn (Builder $builder) => $builder->where('portfolio_id', $portfolioId));
            }
        });
    }

    protected $fillable = [
        'skill_category_id',
        'name',
        'description',
        'level',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'description' => 'array',
            'level' => 'integer',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(SkillCategory::class, 'skill_category_id');
    }
}
