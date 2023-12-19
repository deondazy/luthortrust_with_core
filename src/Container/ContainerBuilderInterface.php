<?php

declare(strict_types=1);

namespace Denosys\Core\Container;

use Psr\Container\ContainerInterface;

interface ContainerBuilderInterface
{
    public function addDefinitions(string $path): self;
    public function enableCompilation(string $path): self;
    public function build(): ContainerInterface;
}
