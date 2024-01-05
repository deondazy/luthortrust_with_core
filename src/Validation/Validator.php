<?php

declare(strict_types=1);

namespace Denosys\Core\Validation;

use Valitron\Validator as ValitronValidator;

class Validator implements ValidatorInterface
{
    /**
     * Validated data
     * 
     * @var array
     */
    private array $validatedData = [];

    /**
     * Validation error messages
     * 
     * @var array
     */
    private array $errors = [];

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
    public function validate(array $data, array $rules): array
    {
        $validator = new ValitronValidator($data);

        foreach ($rules as $field => $fieldRules) {
            foreach ($fieldRules as $ruleString) {
                $rule = RuleFactory::make($ruleString, $data);
                $rule->apply($validator, $ruleString, $field);
            }
        }

        if ($validator->validate()) {
            $this->validatedData = $validator->data();
            return $this->validatedData;
        }

        $this->errors = $validator->errors();
        throw new ValidationException($validator->errors());
    }

    /**
     * Get the validated attributes and values.
     *
     * @return array
     *
     * @throws ValidationException
     */
    public function validated(): array
    {
        if (empty($this->errors)) {
            return $this->validatedData;
        }

        throw new ValidationException($this->errors);
    }

    /**
     * Determine if the data fails the validation rules.
     *
     * @return bool
     */
    public function fails(): bool
    {
        return !empty($this->errors);
    }

    /**
     * Get all the validation error messages.
     *
     * @return array
     */
    public function errors(): array
    {
        return $this->errors;
    }
}
