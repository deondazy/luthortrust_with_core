<?php

declare(strict_types=1);

namespace Denosys\Core\Environment;

interface EnvironmentLoaderInterface
{
    /**
     * Load the environment file at the given path.
     *
     * @param string $path
     * @return void
     */
    public function load(string $path): void;

    /**
     * Get the value of an environment variable, or set a default value.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, mixed $default = null): mixed;
}
