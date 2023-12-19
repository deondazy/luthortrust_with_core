<?php

namespace Denosys\Core\Form\Validation;

use RuntimeException;
use Throwable;

class ValidationException extends RuntimeException
{
    public function __construct(
        protected readonly array $errors,
        string $message = "Form validation error(s)",
        int $code = 422,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
