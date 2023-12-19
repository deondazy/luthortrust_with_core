<?php

declare(strict_types=1);

namespace Denosys\Core\Support;

interface Arrayable
{
    /**
     * Convert the object to its array representation.
     *
     * @return array
     */
    public function toArray(): array;
}