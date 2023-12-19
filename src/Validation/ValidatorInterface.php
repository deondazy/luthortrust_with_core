<?php

declare(strict_types=1);

namespace Denosys\Core\Validation;

interface ValidatorInterface
{
    /**
     * Validates the given data based on the given rules.
     *
     * @param array $data
     * @param array $rules
     *
     * @return array
     *
     * @throws ValidationException
     */
    public function validate(array $data, array $rules): array;

    /**
     * Get the validated attributes and values.
     *
     * @return array
     *
     * @throws ValidationException
     */
    public function validated(): array;

    /**
     * Determine if the data fails the validation rules.
     *
     * @return bool
     */
    public function fails(): bool;

    /**
     * Get all the validation error messages.
     *
     * @return array
     */
    public function errors(): array;
}