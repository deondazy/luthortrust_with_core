<?php

declare(strict_types=1);

namespace Denosys\Core\Support;

use ReflectionClass;
use ReflectionException;

abstract class DataTransferObject implements Arrayable
{
    public static function createFromArray(array $values): static
    {
        try {
            $reflectionClass = new ReflectionClass(static::class);
            $constructor = $reflectionClass->getConstructor();
            $params = $constructor ? $constructor->getParameters() : [];

            // Prepare arguments for the constructor.
            $args = [];
            foreach ($params as $param) {
                $paramName = $param->getName();
                if (array_key_exists($paramName, $values)) {
                    $args[$paramName] = $values[$paramName];
                } else {
                    // Set default or null if the value is not provided.
                    $args[$paramName] = $param->isDefaultValueAvailable() ? $param->getDefaultValue() : null;
                }
            }

            // Instantiate the class with the constructor arguments.
            return $reflectionClass->newInstanceArgs($args);
        } catch (ReflectionException $e) {
            throw new \Exception("Failed to instantiate DataTransferObject: " . $e->getMessage());
        }
    }

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
