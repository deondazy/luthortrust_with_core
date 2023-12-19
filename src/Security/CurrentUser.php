<?php

declare(strict_types=1);

namespace Denosys\Core\Security;

use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class CurrentUser
{
    public function __construct(
        private readonly AuthenticatedTokenStorageInterface $tokenStorage,
        private readonly UserProviderInterface $userProvider
    ) {
    }

    public function getUser(): ?UserInterface
    {
        if (!$this->tokenStorage->isTokenAuthenticated()) {
            return null;
        }

        $userIdentifier = $this->tokenStorage->getToken()?->getUserIdentifier();

        try {
            return $this->userProvider->loadUserByIdentifier($userIdentifier);
        } catch (UserNotFoundException) {
            return null;
        }
    }
}
