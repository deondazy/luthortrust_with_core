<?php

declare(strict_types=1);

namespace Denosys\Core\Support\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class EnvExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('env', 'env'),
        ];
    }
}
