<?php

declare(strict_types=1);

namespace Denosys\Core\View;

use Denosys\Core\Config\ConfigurationInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Views\Twig;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

readonly class ErrorHandler
{
    public function __construct(
        private ConfigurationInterface $config,
        private Twig $twig,
        private ResponseFactoryInterface $responseFactory
    ) {
    }

    /**
     * Handles a given TemplateException.
     *
     * @param TemplateException $exception The template exception to handle.
     *
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws TemplateException
     *
     * @return ResponseInterface The response to be sent.
     */
    public function handleException(TemplateException $exception): ResponseInterface
    {
        if ($this->config->get('app.debug', false) === true) {
            throw $exception;
        }

        return match (get_class($exception)) {
            TemplateNotFoundException::class => $this->renderErrorPage(
                404,
                $this->config->get('views.twig.error_404_template')
            ),
            default => $this->renderErrorPage(500, $this->config->get('views.twig.error_500_template')),
        };
    }

    /**
     * Renders an error page based on the given error code and template.
     *
     * @param int $errorCode The HTTP error code.
     * @param string $template The template to render.
     *
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     *
     * @return ResponseInterface The response to be sent.
     */
    private function renderErrorPage(int $errorCode, string $template): ResponseInterface
    {
        return $this->twig->render($this->responseFactory->createResponse($errorCode), $template);
    }
}
