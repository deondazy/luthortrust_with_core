<?php

declare(strict_types=1);

namespace Denosys\Core\Support;

abstract class DataTransferObject implements Arrayable
{

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        $data = [];

        foreach (get_object_vars($this) as $property => $value) {
            $data[$property] = $value;
        }

        return $data;
    }
}
