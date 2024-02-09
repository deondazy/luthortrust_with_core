<?php

declare(strict_types=1);

namespace Denosys\Core\Filesystem;

use League\Flysystem\PathPrefixer;
use Denosys\Core\Http\UploadedFile;
use Psr\Http\Message\StreamInterface;
use League\Flysystem\UnableToReadFile;
use League\Flysystem\UnableToWriteFile;
use League\Flysystem\FilesystemOperator;
use League\Flysystem\UnableToSetVisibility;
use League\Flysystem\FilesystemAdapter as FlysystemAdapter;

class FilesystemAdapter implements CloudFilesystemInterface
{
    /**
     * League Flysystem PathPrefixer instance.
     *
     * @var PathPrefixer
     */
    protected $prefixer;

    public function __construct(
        protected readonly FilesystemOperator $driver,
        protected readonly FlysystemAdapter $adapter,
        protected readonly array $config = [],
    ) {
        $separator = $config['directory_separator'] ?? DIRECTORY_SEPARATOR;

        $this->prefixer = new PathPrefixer($config['root'] ?? '', $separator);

        if (isset($config['prefix'])) {
            $this->prefixer = new PathPrefixer($this->prefixer->prefixPath($config['prefix']), $separator);
        }
    }

    public function exists($path): bool
    {
        return $this->driver->has($path);
    }

    public function get($path): string
    {
        try {
            return $this->driver->read($path);
        } catch (UnableToReadFile $e) {
            if ($this->throwsExceptions()) {
                throw $e;
            }
        }
    }

    public function readStream(string $path)
    {
        try {
            return $this->driver->readStream($path);
        } catch (UnableToReadFile $e) {
            if ($this->throwsExceptions()) {
                throw $e;
            }
        }
    }

    public function put(string $path, $contents, array $options = []): bool
    {
        if ($contents instanceof UploadedFile) {
            return $this->putFile($path, $contents, $options);
        }

        try {
            if ($contents instanceof StreamInterface) {
                $this->driver->writeStream($path, $contents->detach(), $options);

                return true;
            }

            is_resource($contents)
                ? $this->driver->writeStream($path, $contents, $options)
                : $this->driver->write($path, $contents, $options);
        } catch (UnableToWriteFile | UnableToSetVisibility $e) {
            if ($this->throwsExceptions()) {
                throw $e;
            }

            return false;
        }

        return true;
    }

    public function putFile(
        UploadedFile|string $path,
        UploadedFile|string|array|null $file = null,
        array $options = []
    ): string|false {
        if (is_null($file) || is_array($file)) {
            [$path, $file, $options] = ['', $path, $file ?? []];
        }

        $file = is_string($file) ? new File($file) : $file;

        return $this->putFileAs($path, $file, $file->hashName(), $options);
    }

    public function writeStream(string $path, $resource, array $options = []): bool
    {
        // TODO: implement writeStream() method
    }

    public function getVisibility($path): string
    {
        // TODO: implement getVisibility() method
    }

    public function setVisibility($path, $visibility): bool
    {
        // TODO: implement setVisibility() method
    }

    public function prepend($path, $data): bool
    {
        // TODO: implement prepend() method
    }

    public function append($path, $data): bool
    {
        // TODO: implement append() method
    }

    public function delete($path): bool
    {
        // TODO: implement delete() method
    }

    public function copy($from, $to): bool
    {
        // TODO: implement copy() method
    }

    public function move($from, $to): bool
    {
        // TODO: implement move() method
    }

    public function size($path): int
    {
        // TODO: implement size() method
    }

    public function lastModified($path): int
    {
        // TODO: implement lastModified() method
    }

    public function files(?string $directory = null, bool $recursive = false): array
    {
        // TODO: implement files() method
    }

    public function allFiles(?string $directory = null): array
    {
        // TODO: implement allFiles() method
    }

    public function directories(?string $directory = null, bool $recursive = false): array
    {
        // TODO: implement directories() method
    }

    public function allDirectories(?string $directory = null): array
    {
        // TODO: implement allDirectories() method
    }

    public function makeDirectory($path): bool
    {
        // TODO: implement makeDirectory() method
    }

    public function deleteDirectory($path): bool
    {
        // TODO: implement deleteDirectory() method
    }

    public function url($path): string
    {
        // TODO: implement url() method
    }

    /**
     * Determine if Flysystem exceptions should be thrown.
     *
     * @return bool
     */
    protected function throwsExceptions(): bool
    {
        return (bool) ($this->config['throw'] ?? false);
    }
}
