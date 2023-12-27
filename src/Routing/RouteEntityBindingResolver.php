<?php

declare(strict_types=1);

namespace Denosys\Core\Routing;

use Doctrine\ORM\EntityManagerInterface;
use Invoker\ParameterResolver\ParameterResolver;
use Psr\Http\Message\ServerRequestInterface;
use ReflectionFunctionAbstract;
use Slim\Exception\HttpNotFoundException;

class RouteEntityBindingResolver implements ParameterResolver
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly ServerRequestInterface $request,
    ) {
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

                    $entity = $this->entityManager->find($entityClass, $entityId);

                    if ($entity) {
                        $resolvedParameters[$index] = $entity;
                    } else {
                        throw new HttpNotFoundException($this->request, 'Entity not found');
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
        return class_exists($className) && !empty($this->entityManager->getClassMetadata($className));
    }
}
