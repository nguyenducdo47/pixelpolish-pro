<?php

namespace App\Models;

use App\Support\HasLocaleText;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Education extends Model
{
    use Concerns\BelongsToPortfolio;
    use HasLocaleText;

    protected $table = 'education';

    protected $fillable = [
        'portfolio_id',
        'degree',
        'school',
        'details',
        'period',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'degree' => 'array',
            'school' => 'array',
            'details' => 'array',
        ];
    }

    public function portfolio(): BelongsTo
    {
        return $this->belongsTo(Portfolio::class);
    }
}
