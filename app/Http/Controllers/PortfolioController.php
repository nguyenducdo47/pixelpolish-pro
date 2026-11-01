<?php

namespace App\Http\Controllers;

use App\Enums\ContentProfile;
use App\Models\Portfolio;
use App\Services\CvPdfExporter;
use App\Services\PortfolioPresenter;
use App\Support\ContentProfileLanding;
use App\Support\LocaleCatalog;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class PortfolioController extends Controller
{
    public function __construct(
        private PortfolioPresenter $presenter,
        private CvPdfExporter $cvPdfExporter,
    ) {}

    public function landing(): InertiaResponse
    {
        $locale = app()->getLocale();
        $requested = request()->query('profile', ContentProfile::General->value);
        $active = ContentProfile::tryFrom((string) $requested) ?? ContentProfile::General;
        $options = ContentProfileLanding::profileOptions($locale);
        $activeOption = collect($options)->firstWhere('key', $active->value) ?? ($options[0] ?? null);

        return Inertia::render('Landing', [
            'activeProfile' => $active->value,
            'profileOptions' => $options,
            'demoUrl' => $activeOption['demo_url'] ?? null,
        ]);
    }

    public function show(string $locale, string $username): InertiaResponse
    {
        $portfolio = $this->findPortfolio($username);

        return Inertia::render('Portfolio/Show', $this->payload($portfolio, $locale));
    }

    public function cv(string $locale, string $username): InertiaResponse
    {
        $portfolio = $this->findPortfolio($username);

        return Inertia::render('Portfolio/Cv', $this->payload($portfolio, $locale, $portfolio->resolveCv()));
    }

    public function cvShow(string $locale, string $username, string $cv): InertiaResponse
    {
        $portfolio = $this->findPortfolio($username);

        return Inertia::render('Portfolio/Cv', $this->payload($portfolio, $locale, $portfolio->resolveCv($cv)));
    }

    public function cvPdf(string $locale, string $username, ?string $cv = null): SymfonyResponse
    {
        $portfolio = $this->findPortfolio($username);
        $document = $portfolio->resolveCv($cv);

        $data = $this->payload($portfolio, $locale, $document);

        return $this->cvPdfExporter->download($data, $username, $locale, $document);
    }

    private function findPortfolio(string $username): Portfolio
    {
        $portfolio = Portfolio::query()
            ->where('slug', $username)
            ->firstOrFail();

        abort_unless($portfolio->is_published || $this->canPreview($portfolio), 404);

        return $portfolio;
    }

    private function payload(Portfolio $portfolio, string $locale, ?\App\Models\Cv $cvDocument = null): array
    {
        if ($cvDocument !== null) {
            return $this->presenter->cvPayload($portfolio, $locale, $cvDocument);
        }

        return $this->presenter->publicPayload($portfolio, $locale);
    }

    private function canPreview(Portfolio $portfolio): bool
    {
        $user = auth()->user();

        return $user !== null && ($user->isAdmin() || $user->id === $portfolio->user_id);
    }
}