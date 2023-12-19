<?php

namespace Denosys\Core\Container;

use Exception;
use Psr\Container\ContainerInterface;
use Di\ContainerBuilder as DIContainerBuilder;

class DIContainerAdapter implements ContainerBuilderInterface
{
    public function __construct(private readonly DIContainerBuilder $containerBuilder)
    {
    }

    public function addDefinitions(string $path): self
    {
        $this->containerBuilder->addDefinitions($path);
        return $this;
    }

    public function enableCompilation(string $path): self
    {
        $this->containerBuilder->enableCompilation($path);
        return $this;
    }

    /**
     * @return ContainerInterface
     * @throws Exception
     */
    public function build(): ContainerInterface
    {
        return $this->containerBuilder->build();
    }
}