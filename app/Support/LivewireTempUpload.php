<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Features\SupportFileUploads\FileUploadConfiguration;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Throwable;

final class LivewireTempUpload
{
    public static function ensureDirectories(): void
    {
        foreach ([
            storage_path('app/tmp'),
            storage_path('app/private/livewire-tmp'),
            storage_path('app/public/avatars'),
        ] as $directory) {
            if (is_dir($directory)) {
                continue;
            }

            @mkdir($directory, 0775, true);
        }
    }

    public static function tempDirectory(): string
    {
        self::ensureDirectories();

        $preferred = storage_path('app/tmp');

        if (self::isWritableDirectory($preferred)) {
            return $preferred;
        }

        $fallback = rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR);

        if (self::isWritableDirectory($fallback)) {
            return $fallback;
        }

        throw ValidationException::withMessages([
            'files.0' => __('validation.uploaded', ['attribute' => 'files']),
        ]);
    }

    public static function readableCopy(UploadedFile $file): UploadedFile
    {
        $real = $file->getRealPath();

        if (self::isReadableFile($real)) {
            return $file;
        }

        $destination = self::tempDirectory().DIRECTORY_SEPARATOR.Str::uuid()
            .(filled($file->getClientOriginalExtension()) ? '.'.$file->getClientOriginalExtension() : '');

        $copied = false;

        foreach ([$file->getPathname(), $real] as $source) {
            if (self::isReadableFile($source) && @copy($source, $destination)) {
                $copied = true;
                break;
            }
        }

        if (! $copied) {
            try {
                $contents = $file->getContent();
            } catch (Throwable) {
                $contents = '';
            }

            if ($contents === '') {
                throw ValidationException::withMessages([
                    'files.0' => __('validation.uploaded', ['attribute' => 'files']),
                ]);
            }

            file_put_contents($destination, $contents);
        }

        @chmod($destination, 0664);

        return new UploadedFile(
            $destination,
            $file->getClientOriginalName(),
            $file->getMimeType() ?: $file->getClientMimeType(),
            UPLOAD_ERR_OK,
            true,
        );
    }

    public static function storeOnLivewireDisk(UploadedFile $file, string $disk): string
    {
        $file = self::readableCopy($file);
        $filename = TemporaryUploadedFile::generateHashName($file);
        $contents = self::contents($file);
        $stored = FileUploadConfiguration::directory().'/'.$filename;

        Storage::disk($disk)->put($stored.'.json', json_encode([
            'name' => $file->getClientOriginalName(),
            'type' => $file->getMimeType() ?: $file->getClientMimeType(),
            'size' => strlen($contents),
            'hash' => $file->hashName(),
        ]));

        Storage::disk($disk)->put($stored, $contents);

        return $stored;
    }

    public static function contents(UploadedFile $file): string
    {
        foreach ([$file->getRealPath(), $file->getPathname()] as $source) {
            if (self::isReadableFile($source)) {
                $contents = file_get_contents($source);

                if ($contents !== false && $contents !== '') {
                    return $contents;
                }
            }
        }

        try {
            $contents = $file->getContent();
        } catch (Throwable) {
            $contents = '';
        }

        if ($contents === '') {
            throw ValidationException::withMessages([
                'files.0' => __('validation.uploaded', ['attribute' => 'files']),
            ]);
        }

        return $contents;
    }

    private static function isReadableFile(mixed $path): bool
    {
        return is_string($path) && $path !== '' && is_file($path) && is_readable($path);
    }

    private static function isWritableDirectory(string $path): bool
    {
        return is_dir($path) && is_writable($path);
    }
}
