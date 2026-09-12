<?php

namespace App\Models;

use App\Support\HasLocaleText;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SpokenLanguage extends Model
{
    use Concerns\BelongsToPortfolio;
    use HasLocaleText;

    protected $fillable = [
        'portfolio_id',
        'name',
        'level',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'name' => 'array',
            'level' => 'array',
        ];
    }

    public function portfolio(): BelongsTo
    {
        return $this->belongsTo(Portfolio::class);
    }
}
