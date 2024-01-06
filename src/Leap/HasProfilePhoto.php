<?php

declare(strict_types=1);

namespace Denosys\Core\Leap;

use Denosys\Core\Http\UploadedFile;
use Denosys\Core\Support\Str;

trait HasProfilePhoto
{
    public function updateProfilePhoto(UploadedFile $photo, string $storagePath = 'profile-photos'): void
    {
        // $filesystem = $this->filesystemManager->disk();

        $originalName = $photo->getClientFilename();
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);

        $fileName = Str::random(40) . '.' . $extension;

        $stream = $photo->getStream()->detach();

        $photo->storePublicly($storagePath . $fileName, $stream, ['disk' => 'public']);

        if (is_resource($stream)) {
            $photo->getStream()->close();
        }

        $this->setPassport($fileName);
    }
}
