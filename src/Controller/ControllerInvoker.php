<?php

declare(strict_types=1);

namespace Denosys\Core\Controller;

use Invoker\Exception\InvocationException;
use Invoker\Exception\NotCallableException;
use Invoker\Exception\NotEnoughParametersException;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Interfaces\InvocationStrategyInterface;
use Invoker\InvokerInterface;
use Psr\Http\Message\ResponseInterface;

readonly class ControllerInvoker implements InvocationStrategyInterface
{
    public function __construct(private InvokerInterface $invoker)
    {
    }

    /**
     * @throws InvocationException
     * @throws NotCallableException
     * @throws NotEnoughParametersException
     */
    public function __invoke(
        callable $callable,
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $routeArguments
    ): ResponseInterface {
        $parameters = [];

        // Inject the route arguments by name
        $parameters += $routeArguments;

        // Inject the attributes defined on the request
        $parameters += $request->getAttributes();

        return $this->invoker->call($callable, $parameters);
    }
}
