<?php

namespace App\Support;

use App\Models\Portfolio;
use App\Models\Profile;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\FileUploadConfiguration;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class WizardPendingAvatar
{
    public static function remember(int $portfolioId, mixed $value): void
    {
        $filename = self::filename($value);

        if ($filename !== null) {
            session([self::key($portfolioId) => $filename]);

            return;
        }

        if (self::wasCleared($value)) {
            session()->forget(self::key($portfolioId));
        }
    }

    public static function filename(mixed $value): ?string
    {
        if (is_array($value)) {
            return self::filename($value[0] ?? null);
        }

        if ($value instanceof TemporaryUploadedFile) {
            return $value->exists() ? self::safeName($value->getFilename()) : null;
        }

        return null;
    }

    public static function wasCleared(mixed $value): bool
    {
        if (is_array($value)) {
            return $value === [] || self::wasCleared($value[0] ?? null);
        }

        return $value === null || $value === '' || $value === '[]';
    }

    public static function current(int $portfolioId): ?string
    {
        $filename = self::safeName(session(self::key($portfolioId)));

        if ($filename === null) {
            return null;
        }

        $file = TemporaryUploadedFile::createFromLivewire($filename);

        return $file->exists() ? $filename : null;
    }

    public static function url(int $portfolioId): ?string
    {
        $filename = self::current($portfolioId);

        return $filename
            ? route('studio.preview-avatar', ['v' => $filename], false)
            : null;
    }

    public static function urlFor(Portfolio $portfolio): ?string
    {
        $user = auth()->user();

        if ($user === null) {
            return null;
        }

        if ($user->id !== $portfolio->user_id && ! $user->isAdmin()) {
            return null;
        }

        return self::url($portfolio->id);
    }

    public static function commit(int $portfolioId, mixed $value = null): ?string
    {
        $filename = self::filename($value);

        if ($filename !== null) {
            $path = Profile::storedAvatarPath($value);
            self::forget($portfolioId, $value);

            return $path;
        }

        $existing = is_string($value) ? Profile::storedAvatarPath($value) : null;

        if ($existing !== null) {
            self::forget($portfolioId, $value);

            return $existing;
        }

        $pending = self::current($portfolioId);

        if ($pending !== null) {
            $path = Profile::storedAvatarPath(TemporaryUploadedFile::createFromLivewire($pending));
            self::forget($portfolioId, $value);

            return $path;
        }

        self::forget($portfolioId, $value);

        return self::wasCleared($value) ? null : Profile::storedAvatarPath($value);
    }

    public static function forget(int $portfolioId, mixed $value = null): void
    {
        foreach (self::ownedFilenames($portfolioId, $value) as $filename) {
            self::deleteTemp($filename);
        }

        session()->forget(self::key($portfolioId));
    }

    /**
     * @return list<string>
     */
    public static function ownedFilenames(int $portfolioId, mixed $value = null): array
    {
        $names = [];

        $fromValue = self::filename($value);

        if ($fromValue !== null) {
            $names[] = $fromValue;
        }

        $fromSession = self::safeName(session(self::key($portfolioId)));

        if ($fromSession !== null) {
            $names[] = $fromSession;
        }

        return array_values(array_unique($names));
    }

    public static function response(int $portfolioId): StreamedResponse
    {
        $filename = self::current($portfolioId);

        abort_unless(filled($filename), 404);

        $path = self::tempPath($filename);

        abort_unless(Storage::disk(FileUploadConfiguration::disk())->exists($path), 404);

        return Storage::disk(FileUploadConfiguration::disk())->response($path, $filename, [
            'Cache-Control' => 'private, no-store',
        ]);
    }

    private static function deleteTemp(string $filename): void
    {
        $filename = self::safeName($filename);

        if ($filename === null) {
            return;
        }

        $path = self::tempPath($filename);

        Storage::disk(FileUploadConfiguration::disk())->delete([
            $path,
            $path.'.json',
        ]);
    }

    private static function tempPath(string $filename): string
    {
        $directory = trim((string) FileUploadConfiguration::directory(), '/');

        return ($directory !== '' ? $directory.'/' : '').$filename;
    }

    private static function safeName(mixed $filename): ?string
    {
        if (! is_string($filename) || $filename === '') {
            return null;
        }

        $filename = basename(str_replace('\\', '/', $filename));

        if ($filename === '' || $filename === '.' || $filename === '..') {
            return null;
        }

        return $filename;
    }

    private static function key(int $portfolioId): string
    {
        return 'wizard.pending_avatar.'.$portfolioId;
    }
}
