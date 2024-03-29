<?php

declare(strict_types=1);

namespace Denosys\Core\Filesystem;

interface FilesystemInterface
{
    /**
     * The public visibility setting.
     *
     * @var string
     */
    public const VISIBILITY_PUBLIC = 'public';

    /**
     * The private visibility setting.
     *
     * @var string
     */
    public const VISIBILITY_PRIVATE = 'private';

    /**
     * Determine if a file exists.
     *
     * @param  string  $path
     *
     * @return bool
     */
    public function exists(string $path): bool;

    /**
     * Get the contents of a file.
     *
     * @param  string  $path
     *
     * @return string|null
     */
    public function get(string $path): ?string;

    /**
     * Get a resource to read the file.
     *
     * @param  string  $path
     *
     * @return resource|null The path resource or null on failure.
     */
    public function readStream(string $path);

    /**
     * Write the contents of a file.
     *
     * @param  string  $path
     * @param  \Psr\Http\Message\StreamInterface|\Denosys\Core\Http\UploadedFile|string|resource  $contents
     * @param  array  $options
     *
     * @return bool
     */
    public function put(string $path, $contents, array $options = []): bool;

    /**
     * Write a new file using a stream.
     *
     * @param  string  $path
     * @param  resource  $resource
     * @param  array  $options
     *
     * @return bool
     */
    public function writeStream(string $path, $resource, array $options = []): bool;

    /**
     * Get the visibility for the given path.
     *
     * @param  string  $path
     *
     * @return string
     */
    public function getVisibility(string $path): string;

    /**
     * Set the visibility for the given path.
     *
     * @param  string  $path
     * @param  string  $visibility
     *
     * @return bool
     */
    public function setVisibility(string $path, string $visibility): bool;

    /**
     * Prepend to a file.
     *
     * @param  string  $path
     * @param  string  $data
     *
     * @return bool
     */
    public function prepend(string $path, string $data): bool;

    /**
     * Append to a file.
     *
     * @param  string  $path
     * @param  string  $data
     * @return bool
     */
    public function append(string $path, string $data): bool;

    /**
     * Delete the file at a given path.
     *
     * @param  string|array  $paths
     *
     * @return bool
     */
    public function delete(string|array $paths): bool;

    /**
     * Copy a file to a new location.
     *
     * @param  string  $from
     * @param  string  $to
     *
     * @return bool
     */
    public function copy(string $from, string $to): bool;

    /**
     * Move a file to a new location.
     *
     * @param  string  $from
     * @param  string  $to
     *
     * @return bool
     */
    public function move(string $from, string $to): bool;

    /**
     * Get the file size of a given file.
     *
     * @param  string  $path
     *
     * @return int
     */
    public function size(string $path): int;

    /**
     * Get the file's last modification time.
     *
     * @param  string  $path
     * @return int
     */
    public function lastModified(string $path): int;

    /**
     * Get an array of all files in a directory.
     *
     * @param  string|null  $directory
     * @param  bool  $recursive
     *
     * @return array
     */
    public function files(?string $directory = null, bool $recursive = false): array;

    /**
     * Get all of the files from the given directory (recursive).
     *
     * @param  string|null  $directory
     *
     * @return array
     */
    public function allFiles(?string $directory = null): array;

    /**
     * Get all of the directories within a given directory.
     *
     * @param  string|null  $directory
     * @param  bool  $recursive
     *
     * @return array
     */
    public function directories(?string $directory = null, bool $recursive = false): array;

    /**
     * Get all (recursive) of the directories within a given directory.
     *
     * @param  string|null  $directory
     *
     * @return array
     */
    public function allDirectories(?string $directory = null): array;

    /**
     * Create a directory.
     *
     * @param  string  $path
     * @return bool
     */
    public function makeDirectory(string $path): bool;

    /**
     * Recursively delete a directory.
     *
     * @param  string  $directory
     * @return bool
     */
    public function deleteDirectory(string $directory): bool;
}
