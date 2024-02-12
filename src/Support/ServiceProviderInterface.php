<?php

declare(strict_types=1);

namespace Denosys\Core\Support;

use Psr\Container\ContainerInterface;

interface ServiceProviderInterface
{
    public function __construct(ContainerInterface $container);

    public function register(): void;
}
