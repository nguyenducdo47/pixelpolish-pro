<?php

namespace Tests\Feature;

use App\Http\Controllers\LivewireFileUploadController;
use App\Support\LivewireTempUpload;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\FileUploadConfiguration;
use Livewire\Features\SupportFileUploads\FileUploadController;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Tests\TestCase;

class LivewireTempUploadTest extends TestCase
{
    public function test_file_upload_controller_is_rebound(): void
    {
        $this->assertInstanceOf(LivewireFileUploadController::class, app(FileUploadController::class));
    }

    public function test_readable_copy_keeps_a_usable_file_path(): void
    {
        $original = UploadedFile::fake()->image('avatar.jpg', 80, 80);
        $recovered = LivewireTempUpload::readableCopy($original);

        $this->assertNotFalse($recovered->getRealPath());
        $this->assertFileExists($recovered->getRealPath());
    }

    public function test_readable_copy_recovers_when_realpath_is_empty(): void
    {
        $source = UploadedFile::fake()->image('avatar.jpg', 80, 80);
        $path = $source->getRealPath();
        $this->assertIsString($path);

        $broken = new class($path, 'avatar.jpg', 'image/jpeg', UPLOAD_ERR_OK, true) extends UploadedFile
        {
            public function getRealPath(): string|false
            {
                return false;
            }
        };

        $recovered = LivewireTempUpload::readableCopy($broken);

        $this->assertNotFalse($recovered->getRealPath());
        $this->assertFileExists($recovered->getRealPath());
        $this->assertNotSame($path, $recovered->getRealPath());
    }

    public function test_livewire_can_store_a_temporary_upload(): void
    {
        Storage::fake(FileUploadConfiguration::disk());

        $paths = app(FileUploadController::class)->validateAndStore([
            UploadedFile::fake()->image('avatar.jpg', 80, 80),
        ], FileUploadConfiguration::disk());

        $this->assertCount(1, $paths);
    }

    public function test_temporary_upload_is_written_to_the_livewire_disk(): void
    {
        Storage::fake(FileUploadConfiguration::disk());
        Storage::fake('public');

        $paths = app(FileUploadController::class)->validateAndStore([
            UploadedFile::fake()->image('avatar.jpg', 80, 80),
        ], FileUploadConfiguration::disk());

        $filename = TemporaryUploadedFile::extractPathFromSignedPath($paths->first());
        $this->assertNotFalse($filename);

        $temp = TemporaryUploadedFile::createFromLivewire($filename);
        $this->assertTrue($temp->exists());

        $stored = $temp->storeAs('avatars', 'me.jpg', 'public');
        Storage::disk('public')->assertExists($stored);
    }
}
