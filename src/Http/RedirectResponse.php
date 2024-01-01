<?php

declare(strict_types=1);

namespace Denosys\Core\Http;

use Denosys\Core\Session\SessionInterface;
use Fig\Http\Message\StatusCodeInterface;
use InvalidArgumentException;
use Slim\Psr7\Interfaces\HeadersInterface;
use Slim\Psr7\Response;

use function in_array;

use const ENT_QUOTES;

class RedirectResponse extends Response
{
    public function __construct(
        string $url,
        int $status = StatusCodeInterface::STATUS_FOUND,
        ?HeadersInterface $headers = null,
        protected ?string $targetUrl = null
    ) {
        parent::__construct($status, $headers);

        $this->setTargetUrl($url);

        if (!$this->isRedirect()) {
            throw new InvalidArgumentException(
                sprintf('The HTTP status code is not a redirect ("%s" given).', $status)
            );
        }

        if (
            StatusCodeInterface::STATUS_MOVED_PERMANENTLY == $status
            && !$this->headers->hasHeader('cache-control')
        ) {
            $this->headers->removeHeader('cache-control');
        }
    }

    public function getTargetUrl(): string
    {
        return $this->targetUrl;
    }

    public function setTargetUrl(string $url): self
    {
        if ('' === $url) {
            throw new InvalidArgumentException('Cannot redirect to an empty URL.');
        }

        $this->targetUrl = $url;

        $this->body->write(
            sprintf('<!DOCTYPE html>
                <html>
                    <head>
                        <meta charset="UTF-8" />
                        <meta http-equiv="refresh" content="0;url=\'%1$s\'" />

                        <title>Redirecting to %1$s</title>
                    </head>
                    <body>
                        Redirecting to <a href="%1$s">%1$s</a>.
                    </body>
                </html>', htmlspecialchars($url, ENT_QUOTES, 'UTF-8'))
        );

        $this->headers->setHeader('Location', $url);

        return $this;
    }

    /**
     * Is the response a redirect of some form?
     *
     * @final
     */
    public function isRedirect(string $location = null): bool
    {
        return in_array(
            $this->status,
            [
                StatusCodeInterface::STATUS_CREATED,
                StatusCodeInterface::STATUS_MOVED_PERMANENTLY,
                StatusCodeInterface::STATUS_FOUND,
                StatusCodeInterface::STATUS_SEE_OTHER,
                StatusCodeInterface::STATUS_TEMPORARY_REDIRECT,
                StatusCodeInterface::STATUS_PERMANENT_REDIRECT
            ]
        ) && (null === $location || $location == $this->headers->getHeader('Location'));
    }

    /**
     * Add a flash message to the session.
     *
     * @param string $key The key for the flash message.
     * @param string|array|null $messages The message or messages to flash.
     *
     * @return self
     */
    public function withFlash(string $key, string|array|null $messages = null): self
    {
        $flash = container(SessionInterface::class)->getFlash();

        $flash->add($key, $messages);

        return $this;
    }
}
