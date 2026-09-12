<?php

namespace App\Filament\Widgets;

use App\Filament\Pages\SetupWizard;
use App\Support\PortfolioGuide;
use Filament\Widgets\Widget;

class GettingStartedWidget extends Widget
{
    protected static bool $isDiscovered = false;

    protected static ?int $sort = -2;

    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 'full';

    protected string $view = 'filament.widgets.getting-started';

    public string $activeStep = 'profile';

    public static function canView(): bool
    {
        return auth()->user()?->portfolio !== null;
    }

    public function mount(): void
    {
        $this->activeStep = $this->guide()['first_incomplete'];
    }

    public function selectStep(string $key): void
    {
        $keys = array_column($this->guide()['steps'], 'key');

        if (in_array($key, $keys, true)) {
            $this->activeStep = $key;
        }
    }

    /**
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        $portfolio = auth()->user()->portfolio;
        $guide = $this->guide();
        $active = collect($guide['steps'])->firstWhere('key', $this->activeStep) ?? $guide['steps'][0];

        return [
            ...$guide,
            'active' => $active,
            'wizardUrl' => SetupWizard::getUrl(),
            'previewSite' => $portfolio->publicUrl(),
            'previewCv' => $portfolio->cvUrl(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function guide(): array
    {
        return PortfolioGuide::for(auth()->user()->portfolio);
    }
}
