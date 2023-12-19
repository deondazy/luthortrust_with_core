<?php

declare(strict_types=1);

namespace Denosys\Core\Container;

use DI\ContainerBuilder;
use Exception;
use Psr\Container\ContainerInterface;

class ContainerFactory
{
    /**
     * Build and configure the DI container.
     *
     * @param string $definitions The path to the definitions file.
     * @param bool $isProduction Indicates if the application is in production mode.
     *
     * @throws Exception
     *
     * @return ContainerInterface The configured container.
     */
    public static function build(string $definitions, string $cachePath, bool $isProduction): ContainerInterface
    {
        $containerBuilder = new DIContainerAdapter(new ContainerBuilder());
        $containerBuilder->addDefinitions($definitions);

        if ($isProduction) {
            $containerBuilder->enableCompilation($cachePath);
        }

        return $containerBuilder->build();
    }
}
