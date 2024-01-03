<?php

declare(strict_types=1);

namespace Denosys\Core\Filesystem;

use Denosys\Core\Filesystem\Drivers\LocalFilesystemDriverFactory;
use Denosys\Core\Support\ServiceProvider;
use Denosys\Core\Filesystem\Drivers\AwsS3V3FilesystemDriverFactory;

class FilesystemServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $filesystemConfig = $this->getApplication()->getConfigurations()->get('filesystems');

        // Dynamically register filesystem factories based on the config
        foreach ($filesystemConfig['disks'] as $name => $config) {
            if (isset($config['driver'])) {
                $this->registerFactory($name, $config['driver']);
            }
        }

        // Register the FilesystemManager with the default disk
        $this->container->set(FilesystemManager::class, function () use ($filesystemConfig) {
            $manager = new FilesystemManager($filesystemConfig);
            // Attach all the factories that were registered
            foreach ($filesystemConfig['disks'] as $name => $config) {
                if (isset($config['driver'])) {
                    $factory = $this->container->get('filesystem.factory.' . $config['driver']);
                    $manager->extend($name, $factory);
                }
            }
            return $manager;
        });
    }

    protected function registerFactory($name, $driver): void
    {
        $factoryService = 'filesystem.factory.' . $driver;
        if (!$this->container->has($factoryService)) {
            switch ($driver) {
                case 'local':
                    $this->container->set($factoryService, function () {
                        return new LocalFilesystemDriverFactory();
                    });
                    break;
                case 's3':
                    $this->container->set($factoryService, function () {
                        return new AwsS3V3FilesystemDriverFactory();
                    });
                    break;
            }
        }
    }
}
