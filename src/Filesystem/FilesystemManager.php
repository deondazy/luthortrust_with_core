<?php

declare(strict_types=1);

namespace Denosys\Core\Filesystem;

use Denosys\Core\Config\ConfigurationInterface;

class FilesystemManager
{
    protected $disks = [];

    protected $factories = [];

    public function __construct(protected readonly array $filesystemConfig)
    {
    }

    public function disk($name = null)
    {
        $name = $name ?: $this->getDefaultDisk();

        if (!isset($this->disks[$name])) {
            $this->disks[$name] = $this->createDisk($name);
        }

        return $this->disks[$name];
    }

    public function extend($driver, FilesystemFactoryInterface $factory)
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

    protected function getDefaultDisk()
    {
        return $this->filesystemConfig['default'];
    }
}