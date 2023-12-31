<?php

declare(strict_types=1);

namespace Denosys\Core\Security;

use Denosys\Core\Encryption\EncrypterInterface;
use Denosys\Core\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\NullToken;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class EncryptedSessionTokenStorage implements AuthenticatedTokenStorageInterface
{
    public function __construct(
        private readonly EncrypterInterface $encrypter,
        private readonly SessionInterface $session,
        private readonly string $cookieName = 'session'
    ) {
    }

    /**
     * @inheritDoc
     */
    public function getToken(): ?TokenInterface
    {
        $encryptedToken = $this->session->get($this->cookieName) ?? null;

        if (is_null($encryptedToken)) {
            return new NullToken();
        }

        return $this->encrypter->decrypt($encryptedToken);
    }

    /**
     * @inheritDoc
     */
    public function setToken(?TokenInterface $token): void
    {
        
        $this->session->regenerateId();

        $encryptedToken = $this->encrypter->encrypt($token);

        $this->session->set($this->cookieName, $encryptedToken);
    }

    /**
     * Check if the token is authenticated.
     *
     * @return bool
     */
    public function isTokenAuthenticated(): bool
    {
        $token = $this->getToken();

        return $token !== null && !$token instanceof NullToken;
    }
}
