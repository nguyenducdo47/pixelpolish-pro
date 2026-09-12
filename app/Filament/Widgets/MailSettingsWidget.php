<?php

namespace App\Filament\Widgets;

use App\Filament\Pages\ManageMail;
use Filament\Facades\Filament;
use Filament\Widgets\Widget;

class MailSettingsWidget extends Widget
{
    protected static bool $isDiscovered = false;

    protected static ?int $sort = -1;

    protected int|string|array $columnSpan = 'full';

    protected string $view = 'filament.widgets.mail-settings';

    public static function canView(): bool
    {
        return Filament::getCurrentPanel()?->getId() === 'admin'
            && auth()->user()?->isAdmin() === true
            && ! session()->has('impersonator_id');
    }

    /**
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        return [
            'url' => ManageMail::getUrl(panel: 'admin'),
        ];
    }
}
