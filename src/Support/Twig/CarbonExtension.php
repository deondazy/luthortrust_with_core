<?php

declare(strict_types=1);

namespace Denosys\Core\Support\Twig;

use Carbon\Carbon;
use DateTime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class CarbonExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('diffForHumans', [$this, 'diffForHumans']),
        ];
    }

    // TODO: Add validation for $date
    public function diffForHumans(DateTime $date): string
    {
        return Carbon::parse($date)->diffForHumans();
    }
}
