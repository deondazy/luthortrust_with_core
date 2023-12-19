<?php

declare(strict_types=1);

namespace Denosys\Core\Routing;

use Denosys\Core\Support\ServiceProvider;
use Slim\App;
use Slim\Interfaces\RouteGroupInterface;
use Slim\Interfaces\RouteInterface;

class Route
{
    private static function getApplication(): App
    {
        return ServiceProvider::getApplication()->getContainer()->get(App::class);
    }

    public static function get(string $pattern, mixed $callable): RouteInterface
    {
        return self::getApplication()->get($pattern, $callable);
    }

    public static function post(string $pattern, mixed $callable): RouteInterface
    {
        return self::getApplication()->post($pattern, $callable);
    }

    public static function put(string $pattern, mixed $callable): RouteInterface
    {
        return self::getApplication()->put($pattern, $callable);
    }

    public static function patch(string $pattern, mixed $callable): RouteInterface
    {
        return self::getApplication()->patch($pattern, $callable);
    }

    public static function delete(string $pattern, mixed $callable): RouteInterface
    {
        return self::getApplication()->delete($pattern, $callable);
    }

    public static function options(string $pattern, mixed $callable): RouteInterface
    {
        return self::getApplication()->options($pattern, $callable);
    }

    public static function any(string $pattern, mixed $callable): RouteInterface
    {
        return self::getApplication()->any($pattern, $callable);
    }

    public static function map(array $methods, string $pattern, mixed $callable): RouteInterface
    {
        return self::getApplication()->map($methods, $pattern, $callable);
    }

    public static function group(string $pattern, mixed $callable): RouteGroupInterface
    {
        return self::getApplication()->group($pattern, $callable);
    }

    public static function redirect(string $from, string $to, int $status = 302): RouteInterface
    {
        return self::getApplication()->redirect($from, $to, $status);
    }
}
