<?php

declare(strict_types=1);

use Denosys\Core\Support\Env;
use Denosys\Core\Http\RedirectResponse;
use Denosys\Core\Support\ServiceProvider;
use Fig\Http\Message\StatusCodeInterface;
use Denosys\Core\Config\ConfigurationInterface;
use Denosys\Core\Environment\EnvironmentLoaderInterface;

if (!function_exists('app')) {
    function app(string $abstract = null): mixed
    {
        $app = ServiceProvider::getApplication();

        if (is_null($abstract)) {
            return $app;
        }

        return $app->getContainer()->get($abstract);
    }
}

if (!function_exists('env')) {
    function env(string $key, mixed $default = null): mixed
    {
        $environmentLoader = app(EnvironmentLoaderInterface::class);
        return $environmentLoader->get($key, $default);
    }
}

if (!function_exists('config')) {
    function config(string $key, mixed $default = null): mixed
    {
        $config = app()->getConfigurations();
        return $config->get($key, $default);
    }
}

if (!function_exists('redirectToRoute')) {
    function redirectToRoute(
        string $routeName, 
        array $data = [],
        array $queryParam = [],
        int $status = StatusCodeInterface::STATUS_FOUND
    ): RedirectResponse {
        $routeUrl = app('app')
            ->getRouteCollector()
            ->getRouteParser()
            ->urlFor($routeName, $data, $queryParam);
        
        return new RedirectResponse($routeUrl, $status);

    }
}
