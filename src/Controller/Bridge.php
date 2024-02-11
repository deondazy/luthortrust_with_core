<?php

declare(strict_types=1);

namespace Denosys\Core\Controller;

use Denosys\Core\Http\FormRequestResolver;
use Denosys\Core\Routing\RouteEntityBindingResolver;
use DI\Bridge\Slim\Bridge as BaseBridge;
use Invoker\Invoker;
use Invoker\ParameterResolver\AssociativeArrayResolver;
use Invoker\ParameterResolver\Container\TypeHintContainerResolver;
use Invoker\ParameterResolver\DefaultValueResolver;
use Invoker\ParameterResolver\ResolverChain;
use Psr\Container\ContainerInterface;

class Bridge extends BaseBridge
{
    protected static function createControllerInvoker(ContainerInterface $container): ControllerInvoker
    {
        $resolver = new ResolverChain([
            // Inject route entity bindings first
            new RouteEntityBindingResolver($container),

            // Then inject parameters by name
            new AssociativeArrayResolver(),

            // Then inject ServerRequest for FormRequest objects
            new FormRequestResolver($container),

            // Then inject services by type-hints for those that weren't resolved
            new TypeHintContainerResolver($container),

            // Then fall back on parameters default values for optional route parameters
            new DefaultValueResolver(),
        ]);

        $invoker = new Invoker($resolver, $container);

        return new ControllerInvoker($invoker);
    }
}
