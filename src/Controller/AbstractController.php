<?php

declare(strict_types=1);

namespace Denosys\Core\Controller;

use Denosys\Core\Http\RedirectResponse;
use Denosys\Core\View\Template;
use Denosys\Core\View\TemplateException;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface as Response;

abstract class AbstractController
{
    protected ContainerInterface $container;

    public function setContainer(ContainerInterface $container): void
    {
        $this->container = $container;
    }

    /**
     * Render a view file template
     *
     * @param string $view
     * @param array $data
     *
     * @return Response
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function view(string $view, array $data = []): Response
    {
        $template = $this->container->get(Template::class);

        try {
            return $template->render($view, $data);
        } catch (TemplateException $e) {
            return $template->handleTemplateException($e);
        }
    }

    /**
     * Redirect to a given url
     *
     * @param string $url
     * @param int $status
     *
     * @return Response
     */
    public function redirect(string $url, int $status = StatusCodeInterface::STATUS_FOUND): Response
    {
        return new RedirectResponse($url, $status);
    }

    /**
     * Redirect to a given route
     *
     * @param string $routeName
     * @param array $data
     * @param array $queryParam
     * @param int $status
     *
     * @return Response
     */
    public function redirectToRoute(
        string $routeName,
        array $data = [],
        array $queryParam = [],
        int $status = StatusCodeInterface::STATUS_FOUND
    ): Response {
        return $this->redirect($this->urlFor($routeName, $data, $queryParam), $status);
    }

    public function urlFor(string $routeName, array $data = [], array $queryParams = []): string
    {
        return $this->container->get('app')
            ->getRouteCollector()
            ->getRouteParser()
            ->urlFor($routeName, $data, $queryParams);
    }
}
