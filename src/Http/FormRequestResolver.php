<?php

declare(strict_types=1);

namespace Denosys\Core\Http;

use Slim\Routing\RouteContext;
use ReflectionFunctionAbstract;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Invoker\ParameterResolver\ParameterResolver;

class FormRequestResolver implements ParameterResolver
{
    private $serverRequest;

    public function __construct(private readonly ContainerInterface $container)
    {
        $this->serverRequest = $this->container->get(ServerRequestInterface::class);
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
            $parameterClass = $parameter->getType()->getName();

            if (is_subclass_of($parameterClass, FormRequest::class)) {
                $resolvedParameters[$index] = $this->container->get($parameterClass);
                $resolvedParameters[$index]->setServerRequest($this->serverRequest);
            }
        }

        return $resolvedParameters;
    }
}
