<?php

namespace App\Filament\Pages;

use App\Filament\Concerns\TranslatesPage;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    use TranslatesPage;

    protected static ?string $navigationLabel = 'panel.nav.dashboard';

    protected static ?string $title = 'panel.nav.dashboard';
}
