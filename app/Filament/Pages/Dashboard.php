<?php

namespace App\Filament\Pages;

use App\Filament\Concerns\TranslatesPage;
use App\Filament\Widgets\GettingStartedWidget;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Widgets\AccountWidget;

class Dashboard extends BaseDashboard
{
    use TranslatesPage;

    protected static ?string $navigationLabel = 'panel.nav.dashboard';

    protected static ?string $title = 'panel.nav.dashboard';

    /**
     * @return array<class-string>
     */
    public function getWidgets(): array
    {
        return [
            AccountWidget::class,
            GettingStartedWidget::class,
        ];
    }
}
