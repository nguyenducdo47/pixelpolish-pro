<?php

namespace App\Models;

use App\Enums\CvListType;
use App\Support\HasLocaleText;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CvListItem extends Model
{
    use Concerns\BelongsToPortfolio;
    use HasLocaleText;

    protected $fillable = [
        'portfolio_id',
        'type',
        'title',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'type' => CvListType::class,
            'title' => 'array',
        ];
    }

    public function portfolio(): BelongsTo
    {
        return $this->belongsTo(Portfolio::class);
    }
}
