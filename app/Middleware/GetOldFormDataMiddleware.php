<?php

declare(strict_types=1);

namespace Denosys\App\Middleware;

use Denosys\Core\Session\SessionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Views\Twig;

class GetOldFormDataMiddleware implements MiddlewareInterface
{
    public function __construct(private readonly SessionInterface $session, private readonly Twig $twig)
    {
    }

    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($oldFormData = $this->session->getFlash()->get('oldFormData')) {
            $this->twig->getEnvironment()->addGlobal('old', $oldFormData);
        }

        return $handler->handle($request);
    }
}
