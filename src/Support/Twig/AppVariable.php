<?php

declare(strict_types=1);

namespace Denosys\Core\Support\Twig;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class AppVariable
{
    public function __construct(
        private readonly ?TokenStorageInterface $tokenStorage
    ) {
    }

    /**
     * Returns the current user.
     *
     * @see TokenInterface::getUser()
     */
    public function getUser(): ?UserInterface
    {
        if (!isset($this->tokenStorage)) {
            throw new \RuntimeException('The "app.user" variable is not available.');
        }

        return $this->tokenStorage->getToken()?->getUser();
    }
}