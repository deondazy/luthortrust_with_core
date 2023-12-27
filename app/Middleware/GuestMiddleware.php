<?php

declare(strict_types=1);

namespace Denosys\App\Middleware;

use Denosys\Core\Http\RedirectResponse;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class GuestMiddleware implements MiddlewareInterface
{
    public function __construct(private readonly TokenStorageInterface $tokenStorage)
    {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): Response
    {
        $token = $this->tokenStorage->getToken();

        if ($token !== null && $token->getUser() instanceof UserInterface) {
            return redirectToRoute('account.index');
        }

        return $handler->handle($request);
    }
}
