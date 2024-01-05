<?php

declare(strict_types=1);

namespace Denosys\Core\Validation\Rules;

use Valitron\Validator as ValitronValidator;

interface RuleInterface
{
    public function apply(
        ValitronValidator $validator,
        string $rule,
        string|array $fields
    ): void;
}
