<?php

declare(strict_types=1);

namespace Denosys\Core\Environment;

use Dotenv\Dotenv;
use PhpOption\Option;
use Dotenv\Repository\RepositoryBuilder;
use Dotenv\Repository\RepositoryInterface;
use Dotenv\Repository\Adapter\PutenvAdapter;

class EnvironmentLoader
{
    /**
     * The environment repository instance.
     *
     * @var RepositoryInterface|null
     */
    protected static ?RepositoryInterface $repository = null;

    public static function load(string $path): void
    {
        Dotenv::create(static::getRepository(), $path)->load();
    }

    public static function getRepository(): RepositoryInterface
    {
        if (static::$repository === null) {
            $builder = RepositoryBuilder::createWithDefaultAdapters();
            $builder = $builder->addAdapter(PutenvAdapter::class);
            static::$repository = $builder->immutable()->make();
        }

        return static::$repository;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return Option::fromValue(static::getRepository()->get($key))
            ->map(function ($value) {
                return match (strtolower($value)) {
                    'true', '(true)' => true,
                    'false', '(false)' => false,
                    'empty', '(empty)' => '',
                    'null', '(null)' => null,
                    default => $value,
                };
            })
            ->getOrElse($default);
    }
}
