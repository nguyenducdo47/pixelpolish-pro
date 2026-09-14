<?php

namespace App\Services;

use App\Models\Portfolio;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CvPdfExporter
{
    /**
     * @param  array<string, mixed>  $portfolioPayload  From PortfolioPresenter::publicPayload()
     */
    public function download(array $portfolioPayload, string $username, string $locale): \Symfony\Component\HttpFoundation\Response
    {
        $profile = $portfolioPayload['profile'] ?? [];

        if (! empty($profile['avatar'])) {
            $base64 = $this->avatarToBase64($profile['avatar']);

            if ($base64) {
                $profile['avatar'] = $base64;
                $portfolioPayload['profile'] = $profile;
            }
        }

        $name = $profile['full_name'] ?? $username;
        $filename = Str::slug($name).'-cv-'.$locale.'.pdf';

        return Pdf::loadView('cv.pdf', [
            'portfolio' => $portfolioPayload,
            'forPdf' => true,
        ])
            ->setPaper('a4')
            ->setOption('isHtml5ParserEnabled', true)
            ->download($filename);
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
