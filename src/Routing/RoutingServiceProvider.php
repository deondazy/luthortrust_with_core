<?php

declare(strict_types=1);

namespace Denosys\Core\Routing;

use Denosys\Core\Support\ServiceProvider;
use Slim\Interfaces\RouteParserInterface;

class RoutingServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->registerRouteParser();
        $this->registerRoutes();
    }

    protected function registerRouteParser(): void
    {
        $this->container->set(RouteParserInterface::class, function () {
            return $this
                ->getApplication()
                ->getSlimApp()
                ->getRouteCollector()
                ->getRouteParser();
        });
    }

    protected function registerRoutes(): void
    {
        $routes = $this->getApplication()->basePath('routes/');

        foreach (scandir($routes) as $file) {
            if (is_file($routes . $file)) {
                $route = require $routes . $file;
                $route($this->getApplication()->getSlimApp());
            }
        }
    }
}
