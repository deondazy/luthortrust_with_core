<?php

declare(strict_types=1);

namespace Denosys\Core\Controller;

use Slim\Psr7\Response;
use Invoker\InvokerInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use DI\Bridge\Slim\ControllerInvoker as BaseControllerInvoker;

class ControllerInvoker extends BaseControllerInvoker
{
    private readonly InvokerInterface $invoker;

    public function __construct(InvokerInterface $invoker, private readonly ContainerInterface $container)
    {
        $this->invoker = $invoker;
        parent::__construct($this->invoker);
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

        // return parent::__invoke($callable, $request, $response, $routeArguments);

        // Inject the request and response by parameter name
        $parameters = [
            'request'  => self::injectRouteArguments($request, $routeArguments),
            'response' => $response,
        ];
        // Inject the route arguments by name
        $parameters += $routeArguments;
        // Inject the attributes defined on the request
        $parameters += $request->getAttributes();

        $result = $this->invoker->call($callable, $parameters);

        if ($result instanceof ResponseInterface) {
            return $result;
        }

        $newResponse = new Response();
        if (is_array($result) || is_object($result)) {
            dd(json_encode($result));
            $newResponse->getBody()->write(json_encode($result));
            return $newResponse->withHeader('Content-Type', 'application/json');
        }

        if (is_string($result)) {
            $newResponse->getBody()->write($result);
            return $newResponse;
        }

        // Add additional handling for other types if necessary

        return $newResponse;
    }

    private static function injectRouteArguments(ServerRequestInterface $request, array $routeArguments): ServerRequestInterface
    {
        $requestWithArgs = $request;
        foreach ($routeArguments as $key => $value) {
            $requestWithArgs = $requestWithArgs->withAttribute($key, $value);
        }
        return $requestWithArgs;
    }
}
