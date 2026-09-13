<?php

namespace App\Http\Controllers;

use App\Models\Portfolio;
use App\Services\PortfolioPresenter;
use App\Support\LocaleCatalog;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class PortfolioController extends Controller
{
    public function __construct(private PortfolioPresenter $presenter) {}

    public function landing(): InertiaResponse
    {
        $demo = Portfolio::query()
            ->where('slug', 'nguyenducdo')
            ->where('is_published', true)
            ->first();

        return Inertia::render('Landing', [
            'demoUrl' => $demo
                ? route('portfolio.show', [
                    'locale' => $demo->default_locale ?: LocaleCatalog::defaultCode(),
                    'username' => $demo->slug,
                ])
                : null,
        ]);
    }

    public function show(string $locale, string $username): InertiaResponse
    {
        return Inertia::render('Portfolio/Show', $this->payload($username, $locale));
    }

    public function cv(string $locale, string $username): InertiaResponse
    {
        return Inertia::render('Portfolio/Cv', $this->payload($username, $locale));
    }

    public function cvPdf(string $locale, string $username): SymfonyResponse
    {
        $data = $this->payload($username, $locale);

        if (! empty($data['profile']['avatar'])) {
            $base64 = $this->avatarToBase64($data['profile']['avatar']);

            if ($base64) {
                $data['profile']['avatar'] = $base64;
            }
        }

        $name = $data['profile']['full_name'] ?? $username;
        $filename = Str::slug($name).'-cv-'.$locale.'.pdf';

        return Pdf::loadView('cv.pdf', ['portfolio' => $data])
            ->setPaper('a4')
            ->download($filename);
    }

    private function payload(string $username, string $locale): array
    {
        $portfolio = Portfolio::query()
            ->where('slug', $username)
            ->firstOrFail();

        abort_unless($portfolio->is_published || $this->canPreview($portfolio), 404);

        return $this->presenter->publicPayload($portfolio, $locale);
    }

    private function canPreview(Portfolio $portfolio): bool
    {
        $user = auth()->user();

        return $user !== null && ($user->isAdmin() || $user->id === $portfolio->user_id);
    }

    private function avatarToBase64(string $url): ?string
    {
        $path = parse_url($url, PHP_URL_PATH);

        if (! $path || ! str_contains($path, '/storage/')) {
            return null;
        }

        $relative = Str::after($path, '/storage/');
        $disk = Storage::disk('public');

        if (! $disk->exists($relative)) {
            return null;
        }

        $contents = $disk->get($relative);
        $mime = $disk->mimeType($relative) ?? 'image/jpeg';

        return 'data:'.$mime.';base64,'.base64_encode($contents);
    }
}