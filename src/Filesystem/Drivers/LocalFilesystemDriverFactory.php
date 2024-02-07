<?php

declare(strict_types=1);

namespace Denosys\Core\Filesystem\Drivers;

use League\Flysystem\Filesystem;
use League\Flysystem\Local\LocalFilesystemAdapter;
use Denosys\Core\Filesystem\FilesystemFactoryInterface;

class LocalFilesystemDriverFactory implements FilesystemFactoryInterface
{
    public function make(array $config): Filesystem
    {
        // $visibility = PortableVisibilityConverter::fromArray(
        //     $config['permissions'] ?? [],
        //     $config['directory_visibility'] ?? $config['visibility'] ?? Visibility::PRIVATE
        // );

        // TODO: add support for symlinks
        $adapter = new LocalFilesystemAdapter(
            $config['root'],
            null,
            $config['lock'] ?? LOCK_EX
        );

        return new Filesystem($adapter);
    }
}
