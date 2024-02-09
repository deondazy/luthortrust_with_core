<?php

declare(strict_types=1);

namespace Denosys\Core\Filesystem;

interface CloudFilesystemInterface extends FilesystemInterface
{
    /**
     * Get the URL for the file at the given path.
     *
     * @param  string  $path
     * 
     * @return string
     */
    public function url(string $path): string;
}
