<?php

declare(strict_types=1);

namespace Denosys\Core\Support\Twig;

use Denosys\Core\Config\ConfigurationInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ConfigExtension extends AbstractExtension
{
    public function __construct(private readonly ConfigurationInterface $config)
    {
    }
    public function getFunctions(): array
    {
        return [
            new TwigFunction('config', [$this, 'config']),
        ];
    }

    public function config(string $key, mixed $default = null): mixed
    {
        return $this->config->get($key, $default);
    }
}