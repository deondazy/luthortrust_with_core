<?php

declare(strict_types=1);

namespace Denosys\App\Middleware;

use Denosys\Core\Session\SessionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Views\Twig;

class GetValidationExceptionMiddleware implements MiddlewareInterface
{
    public function __construct(private readonly SessionInterface $session, private readonly Twig $twig)
    {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($errors = $this->session->getFlash()->get('errors')) {
            $this->twig->getEnvironment()->addGlobal('errors', $errors);
        }

        return $handler->handle($request);
    }
}
