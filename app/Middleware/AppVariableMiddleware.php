<?php

declare(strict_types=1);

namespace Denosys\App\Middleware;

use Slim\Views\Twig;
use Psr\Http\Server\MiddlewareInterface;
use Denosys\Core\Support\Twig\AppVariable;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class AppVariableMiddleware implements MiddlewareInterface
{
    public function __construct(private readonly Twig $twig, private readonly AppVariable $appVariable)
    {
    }

    public function process(Request $request, RequestHandler $handler): Response
    {
        $this->twig->getEnvironment()->addGlobal('app', $this->appVariable);

        return $handler->handle($request);
    }
}