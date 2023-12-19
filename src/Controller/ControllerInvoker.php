<?php

declare(strict_types=1);

namespace Denosys\Core\Controller;

use DI\Bridge\Slim\ControllerInvoker as BaseControllerInvoker;
use Invoker\InvokerInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ControllerInvoker extends BaseControllerInvoker
{
    public function __construct(InvokerInterface $invoker, private readonly ContainerInterface $container)
    {
        parent::__construct($invoker);
    }

    public function __invoke(
        callable $callable,
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $routeArguments
    ): ResponseInterface {
        if (is_array($callable) && $callable[0] instanceof AbstractController) {
            $callable[0]->setContainer($this->container);
        }

        return parent::__invoke($callable, $request, $response, $routeArguments);
    }
}
