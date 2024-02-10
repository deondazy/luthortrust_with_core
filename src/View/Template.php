<?php

declare(strict_types=1);

namespace Denosys\Core\View;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Views\Twig;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class Template
{
    public function __construct(
        private readonly Twig $twig,
        private readonly ResponseFactoryInterface $responseFactory,
        private readonly TemplatePathResolver $pathResolver,
        private readonly ErrorHandler $errorHandler
    ) {
    }

    /**
     * Renders a template with the provided data.
     *
     * @param string $template The template name.
     * @param array $data The data to pass to the template.
     *
     * @throws TemplateNotFoundException
     * @throws TemplateRuntimeException
     * @throws TemplateSyntaxException
     *
     * @return ResponseInterface The rendered template as a response.
     */
    public function render(string $template, array $data = []): ResponseInterface
    {
        $templateFile = $this->pathResolver->resolve($template);

        try {
            return $this->twig->render($this->responseFactory->createResponse(), $templateFile, $data);
        } catch (LoaderError $e) {
            throw new TemplateNotFoundException($e->getMessage());
        } catch (SyntaxError $e) {
            throw new TemplateSyntaxException($e->getMessage());
        } catch (RuntimeError $e) {
            throw new TemplateRuntimeException($e->getMessage());
        }
    }

    /**
     * Handles a template exception.
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
    public function handleTemplateException(TemplateException $exception): ResponseInterface
    {
        return $this->errorHandler->handleException($exception);
    }
}
