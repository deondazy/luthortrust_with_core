<?php

declare(strict_types=1);

namespace Denosys\Core\Leap;

use Denosys\Core\Filesystem\FilesystemManager;
use Denosys\Core\Http\UploadedFile;
use Denosys\Core\Support\Str;

trait HasProfilePhoto
{
    public function updateProfilePhoto(UploadedFile $photo, string $storagePath = 'passport-photos/'): void
    {
        // $filesystem = $this->filesystemManager->disk();

        $originalName = $photo->getClientFilename();
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);

        $fileName = $storagePath . Str::random(40) . '.' . $extension;

        $stream = $photo->getStream()->detach();

        $photo->storePublicly($fileName, $stream, ['disk' => 'local']);

        if (is_resource($stream)) {
            fclose($stream);
        }

        $this->setPassport($fileName);
    }

    public function deleteProfilePhoto(): void
    {
        if (!$this->passport) {
            return;
        }

        $filesystem = container(FilesystemManager::class)->disk('local');

        $filesystem->delete($this->passport);
    }
}
