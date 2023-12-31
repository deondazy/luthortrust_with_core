<?php

declare(strict_types=1);

namespace Denosys\App\Middleware;

use Denosys\App\Database\Entities\User;
use Denosys\Core\Security\CurrentUser;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class AdminAccessMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly AuthorizationCheckerInterface $authorization,
        private readonly TokenStorageInterface $tokenStorage,
        private readonly CurrentUser $currentUser,
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): Response
    {
        $userRolesInSession = $this->tokenStorage->getToken()?->getRoleNames() ?? [];
        $userRolesInStorage = $this->currentUser->getUser()?->getRoles() ?? [];

        // Sort the roles before comparing them
        sort($userRolesInSession);
        sort($userRolesInStorage);

        if ($userRolesInSession !== $userRolesInStorage) {
            $this->tokenStorage->setToken(null);
            return redirectToRoute('login');
        }

        if (!$this->authorization->isGranted(User::ROLE_ADMIN)) {
            throw new AccessDeniedException();
        }

        return $handler->handle($request);
    }
}
