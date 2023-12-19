<?php

declare(strict_types=1);

namespace Denosys\Core\Support;

use Denosys\Core\Config\ConfigurationInterface;

class Config
{
    private static ConfigurationInterface $config;

    public static function setConfig(ConfigurationInterface $config): void
    {
        self::$config = $config;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return self::$config->get($key, $default);
    }
}
