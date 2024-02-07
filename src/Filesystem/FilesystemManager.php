<?php

declare(strict_types=1);

namespace Denosys\Core\Filesystem;

use InvalidArgumentException;
use League\Flysystem\Filesystem;

class FilesystemManager
{
    protected array $disks = [];

    protected array $factories = [];

    public function __construct(protected readonly array $filesystemConfig)
    {
    }

    // TODO: return a FilesystemInterface instead of a Filesystem
    public function disk(?string $name = null): Filesystem
    {
        $name = $name ?: $this->getDefaultDisk();

        // if (!isset($this->disks[$name])) {
        //     $this->disks[$name] = $this->createDisk($name);
        // }

        return $this->disks[$name] = $this->get($name);
    }

    public function extend($driver, FilesystemFactoryInterface $factory): void
    {
        $this->factories[$driver] = $factory;
    }

    protected function createDisk($name)
    {
        $config = $this->filesystemConfig['disks'][$name];

        if (!isset($this->factories[$config['driver']])) {
            throw new \Exception("Driver not supported: " . $config['driver']);
        }

        return $this->factories[$config['driver']]->make($config);
    }

    // TODO: return a FilesystemInterface instead of a Filesystem
    protected function get(string $name): Filesystem
    {
        return $this->disks[$name] ?? $this->resolve($name);
    }

    // TODO: return a FilesystemInterface instead of a Filesystem
    protected function resolve(string $name, ?array $config = null): Filesystem
    {
        $config ??= $this->getConfig($name);

        if (empty($config['driver'])) {
            throw new InvalidArgumentException("Disk [$name] does not have a configured driver.");
        }

        $name = $config['driver'];

        return $this->disks[$name] = $this->factories[$config['driver']]->make($config);
    }

    protected function getConfig(string $name): array
    {
        return $this->filesystemConfig['disks'][$name] ?: [];
    }

    protected function getDefaultDisk(): string
    {
        return $this->filesystemConfig['default'];
    }
}