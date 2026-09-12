<?php

namespace App\Models\Concerns;

use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Builder;

trait BelongsToPortfolio
{
    protected static function bootBelongsToPortfolio(): void
    {
        static::creating(function ($model): void {
            if (! $model->portfolio_id && auth()->check()) {
                $portfolio = auth()->user()?->portfolio;
                if ($portfolio) {
                    $model->portfolio_id = $portfolio->id;
                }
            }
        });

        static::addGlobalScope('current_portfolio', function (Builder $query): void {
            if (! Filament::isServing()) {
                return;
            }

            $user = auth()->user();

            if ($user instanceof User && $user->portfolio) {
                $query->where($query->getModel()->getTable().'.portfolio_id', $user->portfolio->id);
            }
        });
    }
}
