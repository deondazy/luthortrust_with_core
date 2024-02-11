<?php

declare(strict_types=1);

use Denosys\Core\Application;
use Denosys\Core\Http\RedirectResponse;
use Doctrine\ORM\EntityManagerInterface;
use Fig\Http\Message\StatusCodeInterface;
use Denosys\Core\Environment\EnvironmentLoader;

if (!function_exists('container')) {
    function container(string $abstract = null): mixed
    {
        $container = Application::getContainer();

        if (is_null($abstract)) {
            return $container;
        }

        return $container->get($abstract);
    }
}

if (!function_exists('env')) {
    function env(string $key, mixed $default = null): mixed
    {
        return EnvironmentLoader::get($key, $default);
    }
}

if (!function_exists('config')) {
    function config(string $key, mixed $default = null): mixed
    {
        $config = container('config');
        return $config->get($key, $default);
    }
}

if (!function_exists('redirectToRoute')) {
    /**
     * Redirects to a specified route.
     *
     * @param string $routeName The name of the route to redirect to.
     * @param array $data Optional data to be used in generating the route URL.
     * @param array $queryParam Optional query parameters to be appended to the route URL.
     * @param int $status The HTTP status code to be used for the redirect response.
     *
     * @return RedirectResponse The redirect response object.
     */
    function redirectToRoute(
        string $routeName,
        array $data = [],
        array $queryParam = [],
        int $status = StatusCodeInterface::STATUS_FOUND
    ): RedirectResponse {
        $routeUrl = container('app')
            ->getRouteCollector()
            ->getRouteParser()
            ->urlFor($routeName, $data, $queryParam);

        return new RedirectResponse($routeUrl, $status);
    }
}

if (!function_exists('fake') && class_exists(\Faker\Factory::class)) {
    /**
     * Get a faker instance.
     *
     * @param  string|null  $locale
     * @return \Faker\Generator
     */
    function fake(string $locale = null): \Faker\Generator
    {
        // TODO: Add support for locale from config
        if (container()->has('config')) {
            $locale ??= container()->get('config')->get('app.faker_locale');
        }

        $locale ??= 'en_US';

        $abstract = \Faker\Generator::class . ':' . $locale;

        if (!container()->has($abstract)) {
            container()->set($abstract, function () use ($locale) {
                return \Faker\Factory::create($locale);
            });
        }

        return container()->get($abstract);
    }
}

if (!function_exists('entityManager')) {
    /**
     * Get the entity manager instance.
     *
     * @return EntityManagerInterface
     */
    function entityManager(): EntityManagerInterface
    {
        return container(EntityManagerInterface::class);
    }
}
