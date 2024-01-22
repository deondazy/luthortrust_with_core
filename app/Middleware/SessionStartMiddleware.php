<?php

declare(strict_types=1);

namespace Denosys\App\Middleware;

use Denosys\Core\Config\ConfigurationInterface;
use Denosys\Core\Encryption\DecryptException;
use Denosys\Core\Encryption\EncrypterInterface;
use Denosys\Core\Http\RedirectResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Denosys\Core\Session\SessionManagerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Cookies;

class SessionStartMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly SessionManagerInterface $session,
        private readonly EncrypterInterface $encrypter,
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $sessionName = config('session.name');
        if (container('cookie')->get($sessionName) !== null) {
            $encryptedSessionId = $_COOKIE[$sessionName];

            try {
                $decryptedSessionId = $this->encrypter->decrypt($encryptedSessionId);
                session_id($decryptedSessionId);
            } catch (DecryptException) {
                return new RedirectResponse('/login');
            }
        }

        if (!$this->session->isStarted()) {
            $this->session->start();
        }

        $request = $request->withSession($this->session);

        $response = $handler->handle($request);

        $this->session->save();

        return $response;
    }
}
