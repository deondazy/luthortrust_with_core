<?php

declare(strict_types=1);

namespace Denosys\Core\Support\Twig;

use Denosys\Core\Session\SessionInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class FlashExtension extends AbstractExtension
{
    public function __construct(private readonly SessionInterface $session)
    {
    }

    public function hasFlashMessage(string $key): bool
    {
        return $this->session->getFlash()->has($key);
    }

    public function getFlashMessage(string $key): string|array|null
    {
        return $this->session->getFlash()->get($key);
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('has_flash', $this->hasFlashMessage(...)),
            new TwigFunction('get_flash', $this->getFlashMessage(...)),
        ];
    }
}
