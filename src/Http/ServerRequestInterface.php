<?php

declare(strict_types=1);

namespace Denosys\Core\Http;

use Denosys\Core\Session\SessionInterface;
use Psr\Http\Message\ServerRequestInterface as PsrServerRequestInterface;

interface ServerRequestInterface extends PsrServerRequestInterface
{
    /**
     * Check if the server request has a session.
     *
     * @return bool True if the server request has a session, false otherwise.
     */
    public function hasSession(): bool;

    /**
     * Get the session associated with the server request.
     *
     * @return SessionInterface The session object.
     */
    public function getSession(): SessionInterface;

    /**
     * Returns a new instance of the server request with the provided session.
     *
     * @param SessionInterface $session The session to be associated with the server request.
     * 
     * @return static
     */
    public function withSession(SessionInterface $session): static;
}