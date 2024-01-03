<?php

declare(strict_types=1);

namespace Denosys\Core\Filesystem\Drivers;

use League\Flysystem\Filesystem;
use League\Flysystem\Visibility;
use League\Flysystem\Local\LocalFilesystemAdapter;
use Denosys\Core\Filesystem\FilesystemFactoryInterface;
use League\Flysystem\UnixVisibility\PortableVisibilityConverter;

class LocalFilesystemDriverFactory implements FilesystemFactoryInterface
{
    public function make(array $config): Filesystem
    {
        $visibility = PortableVisibilityConverter::fromArray(
            $config['permissions'] ?? [],
            $config['directory_visibility'] ?? $config['visibility'] ?? Visibility::PRIVATE
        );

        // TODO: add support for symlinks
        $adapter = new LocalFilesystemAdapter(
            $config['root'],
            $visibility,
            $config['lock'] ?? LOCK_EX
        );
        
        return new Filesystem($adapter);
    }
}
