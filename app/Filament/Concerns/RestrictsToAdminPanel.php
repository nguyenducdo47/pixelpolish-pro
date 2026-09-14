<?php

namespace App\Filament\Concerns;

use Filament\Facades\Filament;

trait RestrictsToAdminPanel
{
    public static function canViewAny(): bool
    {
        return auth()->user()?->isAdmin() === true
            && ! session()->has('impersonator_id')
            && Filament::getCurrentPanel()?->getId() === 'admin';
    }
}
