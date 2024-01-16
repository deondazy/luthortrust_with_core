<?php

declare(strict_types=1);

namespace Denosys\App\Middleware;

use Slim\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ResponseConversionMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);

        if ($response instanceof ResponseInterface) {
            return $response;
        }

        $newResponse = new Response();
        if (is_array($response) || is_object($response)) {
            $newResponse->getBody()->write(json_encode($response));
            return $newResponse->withHeader('Content-Type', 'application/json');
        }

        if (is_string($response)) {
            $newResponse->getBody()->write($response);
            return $newResponse;
        }

        return $newResponse;
    }
}
