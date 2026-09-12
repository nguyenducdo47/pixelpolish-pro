<?php

namespace App\Http\Controllers;

use App\Support\LivewireTempUpload;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Livewire\Features\SupportFileUploads\FileUploadConfiguration;
use Livewire\Features\SupportFileUploads\FileUploadController;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class LivewireFileUploadController extends FileUploadController
{
    public function validateAndStore($files, $disk)
    {
        Validator::make(['files' => $files], [
            'files.*' => FileUploadConfiguration::rules(),
        ])->validate();

        return collect($files)->map(function (mixed $file) use ($disk): string {
            if (! $file instanceof UploadedFile) {
                throw ValidationException::withMessages([
                    'files.0' => __('validation.uploaded', ['attribute' => 'files']),
                ]);
            }

            $path = LivewireTempUpload::storeOnLivewireDisk($file, $disk);
            $stripped = str_replace(FileUploadConfiguration::path('/'), '', $path);

            return TemporaryUploadedFile::signPath($stripped);
        });
    }
}
