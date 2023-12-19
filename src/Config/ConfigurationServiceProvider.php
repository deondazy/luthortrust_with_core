<?php

declare(strict_types=1);

namespace Denosys\Core\Config;

use Denosys\Core\Support\ServiceProvider;

class ConfigurationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->container->set(ConfigurationInterface::class, function () {
            $config = (new ConfigurationManager())->loadConfigurationFiles(
                self::getApplication()->basePath('config/')
            );

            return new ArrayFileConfiguration($config);
        });
    }
}
