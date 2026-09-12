<?php

namespace App\Models;

use App\Support\HasLocaleText;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Principle extends Model
{
    use Concerns\BelongsToPortfolio;
    use HasLocaleText;

    protected $fillable = [
        'portfolio_id',
        'title',
        'description',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'title' => 'array',
            'description' => 'array',
        ];
    }

    public function portfolio(): BelongsTo
    {
        return $this->belongsTo(Portfolio::class);
    }
}
