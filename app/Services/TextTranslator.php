<?php

namespace App\Services;

use App\Models\TranslationApi;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;

class TextTranslator
{
    public function translate(string $text, string $target, string $source = 'auto'): string
    {
        $text = trim($text);
        $target = $this->normalize($target);
        $source = $source === 'auto' ? 'auto' : $this->normalize($source);

        if ($text === '') {
            return '';
        }

        if ($source !== 'auto' && $source === $target) {
            return $text;
        }

        $translated = [];

        foreach ($this->chunks($text) as $chunk) {
            $translated[] = $this->translateChunk($chunk, $target, $source);
        }

        return trim(implode(' ', $translated));
    }

    protected function translateChunk(string $text, string $target, string $source): string
    {
        foreach (TranslationApi::active() as $api) {
            $translated = $this->viaDriver($api, $text, $target, $source);

            if (filled($translated)) {
                return $translated;
            }
        }

        throw new RuntimeException('Translation failed.');
    }

    protected function viaDriver(TranslationApi $api, string $text, string $target, string $source): ?string
    {
        return match ($api->driver) {
            'mymemory' => $this->viaMyMemory($api, $text, $target, $source),
            'google' => $this->viaGoogle($api, $text, $target, $source),
            'google_chrome' => $this->viaGoogleChrome($api, $text, $target, $source),
            default => null,
        };
    }

    protected function viaMyMemory(TranslationApi $api, string $text, string $target, string $source): ?string
    {
        $pairSource = $source === 'auto' ? $this->guessSource($text, $target) : $source;
        $payload = [
            'q' => $text,
            'langpair' => $pairSource.'|'.$target,
        ];

        try {
            $response = $this->request(
                fn (PendingRequest $http) => $api->usesPost()
                    ? $http->asForm()->post($api->url, $payload)
                    : $http->get($api->url, $payload),
                $api->name,
            );
        } catch (Throwable) {
            return null;
        }

        if (! $response->successful() || (int) $response->json('responseStatus') !== 200) {
            Log::warning('MyMemory translation rejected.', [
                'status' => $response->status(),
                'body' => mb_substr($response->body(), 0, 300),
            ]);

            return null;
        }

        $translated = trim((string) $response->json('responseData.translatedText'));

        if ($translated === '' || str_contains(strtoupper($translated), 'MYMEMORY WARNING')) {
            return null;
        }

        return html_entity_decode($translated, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    protected function viaGoogle(TranslationApi $api, string $text, string $target, string $source): ?string
    {
        try {
            $response = $this->request(
                fn (PendingRequest $http) => $this->send($http, $api, [
                    'client' => 'gtx',
                    'sl' => $source,
                    'tl' => $target,
                    'dt' => 't',
                    'q' => $text,
                ]),
                $api->name,
            );
        } catch (Throwable) {
            return null;
        }

        if (! $response->successful()) {
            Log::warning('Google translation rejected.', [
                'status' => $response->status(),
                'body' => mb_substr($response->body(), 0, 300),
            ]);
        }

        return $this->parseGoogleSegments($response);
    }

    protected function viaGoogleChrome(TranslationApi $api, string $text, string $target, string $source): ?string
    {
        try {
            $response = $this->request(
                function (PendingRequest $http) use ($api, $text, $target, $source) {
                    if (filled($api->user_agent)) {
                        $http = $http->withUserAgent($api->user_agent);
                    }

                    return $this->send($http, $api, [
                        'client' => 'dict-chrome-ex',
                        'sl' => $source,
                        'tl' => $target,
                        'q' => $text,
                    ]);
                },
                $api->name,
            );
        } catch (Throwable) {
            return null;
        }

        if (! $response->successful()) {
            Log::warning('Google Chrome translation rejected.', [
                'status' => $response->status(),
                'body' => mb_substr($response->body(), 0, 300),
            ]);

            return null;
        }

        $translated = $this->extractChromeTranslation($response->json());

        if (filled($translated)) {
            return $translated;
        }

        Log::warning('Google Chrome translation could not be parsed.', [
            'body' => mb_substr($response->body(), 0, 300),
        ]);

        return $this->parseGoogleSegments($response);
    }

    /**
     * Chrome returns [["Hello world","vi"]] instead of the classic
     * [[["Hello world","Xin chào thế giới", ...]]] Google payload.
     */
    protected function extractChromeTranslation(mixed $json): ?string
    {
        if (is_string($json) && filled(trim($json))) {
            return $json;
        }

        if (! is_array($json) || $json === []) {
            return null;
        }

        $first = $json[0] ?? null;

        if (is_string($first) && filled(trim($first))) {
            return $first;
        }

        if (! is_array($first) || $first === []) {
            return null;
        }

        $inner = $first[0] ?? null;

        if (is_string($inner) && filled(trim($inner))) {
            return $inner;
        }

        if (! is_array($inner)) {
            return null;
        }

        $translated = collect($first)
            ->map(function (mixed $segment): string {
                if (is_string($segment)) {
                    return $segment;
                }

                return is_array($segment) ? (string) ($segment[0] ?? '') : '';
            })
            ->implode('');

        return filled($translated) ? $translated : null;
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    protected function send(PendingRequest $http, TranslationApi $api, array $payload): Response
    {
        return $api->usesPost()
            ? $http->asForm()->post($api->url, $payload)
            : $http->get($api->url, $payload);
    }

    protected function parseGoogleSegments(Response $response): ?string
    {
        if (! $response->successful()) {
            return null;
        }

        $segments = $response->json(0);

        if (! is_array($segments)) {
            $decoded = json_decode($response->body(), true);
            $segments = is_array($decoded) ? ($decoded[0] ?? null) : null;
        }

        if (! is_array($segments)) {
            return null;
        }

        $translated = collect($segments)
            ->map(fn ($segment) => is_array($segment) ? (string) ($segment[0] ?? '') : '')
            ->implode('');

        return filled($translated) ? $translated : null;
    }

    /**
     * @param  callable(PendingRequest): Response  $callback
     */
    protected function request(callable $callback, string $provider): Response
    {
        try {
            return $callback($this->http(true));
        } catch (Throwable $e) {
            Log::warning($provider.' translation request failed, retrying without SSL verify.', [
                'error' => $e->getMessage(),
            ]);

            return $callback($this->http(false));
        }
    }

    protected function http(bool $verify): PendingRequest
    {
        return Http::timeout(25)
            ->connectTimeout(10)
            ->withOptions(['verify' => $verify]);
    }

    protected function guessSource(string $text, string $target): string
    {
        if (preg_match('/[àáạảãăằắặẳẵâầấậẩẫèéẹẻẽêềếệểễìíịỉĩòóọỏõôồốộổỗơờớợởỡùúụủũưừứựửữỳýỵỷỹđ]/iu', $text)) {
            return 'vi';
        }

        return $target === 'vi' ? 'en' : 'vi';
    }

    /**
     * @return list<string>
     */
    protected function chunks(string $text): array
    {
        if (mb_strlen($text) <= 400) {
            return [$text];
        }

        $parts = preg_split('/(?<=[.!?。！？\n])\s+/u', $text) ?: [$text];
        $chunks = [];
        $buffer = '';

        foreach ($parts as $part) {
            if ($buffer !== '' && mb_strlen($buffer.' '.$part) > 400) {
                $chunks[] = $buffer;
                $buffer = $part;

                continue;
            }

            $buffer = $buffer === '' ? $part : $buffer.' '.$part;
        }

        if ($buffer !== '') {
            $chunks[] = $buffer;
        }

        return $chunks === [] ? [$text] : $chunks;
    }

    protected function normalize(string $code): string
    {
        $code = strtolower(str_replace('_', '-', trim($code)));

        return match ($code) {
            'zh', 'zh-cn' => 'zh-CN',
            'zh-tw' => 'zh-TW',
            default => explode('-', $code)[0],
        };
    }
}
