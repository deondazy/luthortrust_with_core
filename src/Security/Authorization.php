<?php

declare(strict_types=1);

namespace Denosys\Core\Security;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class Authorization
{
    public function __construct(
        private readonly TokenStorageInterface $tokenStorage,
        private readonly AccessDecisionManagerInterface $accessDecisionManager
    ) {
    }

    /**
     * Get the user token.
     *
     * @return TokenInterface|null
     */
    public function getToken(): ?TokenInterface
    {
        return $this->tokenStorage->getToken();
    }

    /**
     * Authorize the user.
     *
     * @param array<string> $attributes
     *
     * @throws AccessDeniedException
     *
     * @return void
     */
    public function authorize(array $attributes): void
    {
        $token = $this->getToken();

        if (null === $token) {
            throw new AccessDeniedException();
        }

        if (!$this->accessDecisionManager->decide($token, $attributes)) {
            throw new AccessDeniedException();
        }
    }

    /**
     * Check if the user is authorized.
     *
     * @param array<string> $attributes
     *
     * @return bool
     */
    public function isAuthorized(array $attributes): bool
    {
        $token = $this->getToken();

        if ($token === null) {
            return false;
        }

        return $this->accessDecisionManager->decide($token, $attributes);
    }

    /**
     * Check if user has role.
     *
     * @param string $role
     *
     * @return bool
     */
    public function hasRole(string $role): bool
    {
        $token = $this->getToken();

        if ($token === null) {
            return false;
        }

        return in_array($role, $token->getRoleNames());
    }
}
