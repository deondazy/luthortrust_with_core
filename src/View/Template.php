<?php

declare(strict_types=1);

namespace Denosys\Core\View;

use Psr\Http\Message\ResponseInterface;
use Slim\Psr7\Response;
use Slim\Views\Twig;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

readonly class Template
{
    public function __construct(private TemplatePathResolver $pathResolver)
    {
    }

    /**
     * Renders a template with the provided data.
     *
     * @param string $template The template name.
     * @param array $data The data to pass to the template.
     *
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     *
     * @return ResponseInterface The rendered template as a response.
     */
    public function render(string $template, array $data = []): ResponseInterface
    {
        $templateFile = $this->pathResolver->resolve($template);

        return container(Twig::class)->render(new Response(), $templateFile, $data);
    }
}
