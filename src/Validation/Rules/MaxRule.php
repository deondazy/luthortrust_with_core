<?php

declare(strict_types=1);

namespace Denosys\Core\Validation\Rules;

use Valitron\Validator as ValitronValidator;

class MaxRule implements RuleInterface
{
    public function __construct(private string $parameter)
    {
    }

    public function apply(ValitronValidator $validator, string $rule, string|array $fields): void
    {
        $validator->rule('lengthMax', $fields, $this->parameter);
    }
}
