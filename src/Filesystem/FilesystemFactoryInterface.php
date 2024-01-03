<?php

declare(strict_types=1);

namespace Denosys\Core\Filesystem;

use League\Flysystem\Filesystem;

interface FilesystemFactoryInterface
{
    public function make(array $config): Filesystem;
}