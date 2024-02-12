<?php

declare(strict_types=1);

namespace Denosys\Core\Support;

use Denosys\Core\Application;
use Psr\Container\ContainerInterface;
use RuntimeException;

abstract class ServiceProvider implements ServiceProviderInterface
{
    private static ?Application $app = null;

    public function __construct(protected readonly ContainerInterface $container)
    {
    }

    abstract public function register(): void;

    public static function setApplication(Application $app): void
    {
        self::$app = $app;
        self::registerServiceProviders();
    }

    private static function registerServiceProviders(): void
    {
        $container = self::$app->getContainer();
        $providers = require self::$app->basePath('config/providers.php');

        foreach ($providers as $providerClass) {
            if (class_exists($providerClass)) {
                $provider = new $providerClass($container);
                $provider->register();
            }
        }
    }

    public static function getApplication(): Application
    {
        if (self::$app === null) {
            throw new RuntimeException("Application has not been initialized.");
        }
        return self::$app;
    }
}
