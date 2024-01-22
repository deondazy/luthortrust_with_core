<?php

declare(strict_types=1);

namespace Denosys\Core\Http;

use Slim\Psr7\Request as SlimRequest;
use Denosys\Core\Session\SessionInterface;
use Denosys\Core\Session\SessionNotFoundException;

class Request extends SlimRequest
{
    /**
     * @var SessionInterface|null
     */
    protected ?SessionInterface $session = null;

    /**
     * Get the session.
     * 
     * @return SessionInterface
     * 
     * @throws SessionNotFoundException
     */
    public function getSession(): SessionInterface
    {
        $session = $this->session;

        if (null === $session) {
            throw new SessionNotFoundException(
                'The session is not available. Please add the SessionMiddleware to your application.'
            );
        }

        return $session;
    }

    /**
     * Check if the has a Session object.
     * 
     * @return bool
     */
    public function hasSession(): bool
    {
        return null !== $this->session && $this->session instanceof SessionInterface;
    }

    /**
     * Return a new instance with the specified session object.
     * 
     * @param SessionInterface $session
     * 
     * @return void
     */
    public function withSession(SessionInterface $session): static
    {
        $clone = clone $this;
        $clone->session = $session;
        
        return $clone;
    }
}
