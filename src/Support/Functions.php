<?php

declare(strict_types=1);

use Denosys\Core\Config\ConfigurationInterface;
use Denosys\Core\Support\Env;
use Denosys\Core\Environment\EnvironmentLoaderInterface;
use Denosys\Core\Support\ServiceProvider;

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
