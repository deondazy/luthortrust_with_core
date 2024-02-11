<?php

declare(strict_types=1);

namespace Denosys\Core\Routing;

use ReflectionFunctionAbstract;
use Psr\Container\ContainerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Slim\Exception\HttpNotFoundException;
use Psr\Http\Message\ServerRequestInterface;
use Invoker\ParameterResolver\ParameterResolver;

class RouteEntityBindingResolver implements ParameterResolver
{
    public function __construct(private readonly ContainerInterface $container)
    {
    }

    public function getParameters(
        ReflectionFunctionAbstract $reflection,
        array $providedParameters,
        array $resolvedParameters
    ): array {
        $parameters = $reflection->getParameters();

        // Skip parameters already resolved
        if (!empty($resolvedParameters)) {
            $parameters = array_diff_key($parameters, $resolvedParameters);
        }

        foreach ($parameters as $index => $parameter) {
            if (array_key_exists($parameter->name, $providedParameters)) {
                $entityId = $providedParameters[$parameter->name];

                // Check if entityId is not valid or parameter allows null
                if (!$entityId || $parameter->allowsNull()) {
                    continue;
                }

                $type = $parameter->getType();

                if ($type && $type instanceof \ReflectionNamedType && !$type->isBuiltin()) {
                    $entityClass = $type->getName();

                    if (!$this->isDoctrineEntity($entityClass)) {
                        continue; // Skip to the next parameter if not a Doctrine entity
                    }

                    $entity = $this->getEntityManager()->find($entityClass, $entityId);

                    if ($entity) {
                        $resolvedParameters[$index] = $entity;
                    } else {
                        $request = $this->container->get(ServerRequestInterface::class);
                        throw new HttpNotFoundException($request, 'Entity not found');
                    }
                }
            }
        }

        return $resolvedParameters;
    }

    /**
     * Check if the given class name is a Doctrine entity.
     *
     * @param string $className
     * @return bool
     */
    private function isDoctrineEntity(string $className): bool
    {
        return class_exists($className) && !empty($this->getEntityManager()->getClassMetadata($className));
    }

    private function getEntityManager(): EntityManagerInterface
    {
        return $this->container->get(EntityManagerInterface::class);
    }
}
