<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PortfolioTheme extends Model
{
    protected $fillable = [
        'portfolio_id',
        'theme_id',
        'customization',
    ];

    protected function casts(): array
    {
        return [
            'customization' => 'array',
        ];
    }

    public function portfolio(): BelongsTo
    {
        return $this->belongsTo(Portfolio::class);
    }

    public function theme(): BelongsTo
    {
        return $this->belongsTo(Theme::class);
    }
}
