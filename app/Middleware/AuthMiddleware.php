<?php

declare(strict_types=1);

namespace Denosys\App\Middleware;

use Denosys\App\Database\Entities\User;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Symfony\Component\Security\Core\Authentication\AuthenticationTrustResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManager;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
use Symfony\Component\Security\Core\Authorization\Voter\RoleHierarchyVoter;
use Symfony\Component\Security\Core\Authorization\Voter\RoleVoter;
use Symfony\Component\Security\Core\Role\RoleHierarchy;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Denosys\Core\Http\RedirectResponse;

class AuthMiddleware implements MiddlewareInterface
{
    public function __construct(private readonly TokenStorageInterface $tokenStorage)
    {
    }

    public function process(Request $request, RequestHandler $handler): Response
    {
        $token = $this->tokenStorage->getToken();

        if ($token === null) {
            return new RedirectResponse('/login');
        }

        $accessDecisionManager = new AccessDecisionManager([
            new AuthenticatedVoter(new AuthenticationTrustResolver()),
            new RoleVoter(),
            new RoleHierarchyVoter(new RoleHierarchy([
                User::ROLE_ADMIN => [User::ROLE_USER],
            ]))
        ]);

        if (!$accessDecisionManager->decide($token, [User::ROLE_USER])) {
            return new RedirectResponse('/login');
        }

        return $handler->handle($request);
    }
}
