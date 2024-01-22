<?php

declare(strict_types=1);

namespace Denosys\App\Middleware;

use Denosys\Core\Encryption\DecryptException;
use Denosys\Core\Encryption\EncrypterInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Denosys\Core\Session\SessionManagerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

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
        $cookie = container('cookie')->get($sessionName);
        if (null !== $cookie) {
            try {
                session_id($this->encrypter->decrypt($cookie));
            } catch (DecryptException) {
                return redirectToRoute('login');
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
