<?php

declare(strict_types=1);

namespace Denosys\Core\Support\Twig;

use Psr\Http\Message\ServerRequestInterface;
use Slim\Routing\RouteContext;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class UrlExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('url_is', [$this, 'urlIs']),
        ];
    }

    public function urlIs($pattern, $currentUrl): bool
    {
        $pattern = '#^' . str_replace('\*', '.*', preg_quote($pattern, '#')) . '$#';

        if (preg_match($pattern, $currentUrl)) {
            return true;
        }

        return false;
    }
}
