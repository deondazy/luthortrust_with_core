<?php

declare(strict_types=1);

namespace Denosys\Core\Validation;

use Doctrine\Inflector\Inflector;
use Doctrine\Inflector\InflectorFactory;
use Doctrine\ORM\EntityManagerInterface;
use Valitron\Validator as ValitronValidator;

class Validator implements ValidatorInterface
{
    /**
     * Entity manager instance
     *
     * @var EntityManagerInterface
     */
    private ?EntityManagerInterface $entityManager = null;

    /**
     * Inflector instance
     *
     * @var Inflector|null
     */
    private ?Inflector $inflector = null;

    /**
     * Default namespace for Entities
     *
     * @var string
     */
    private string $namespace = 'Denosys\\App\\Database\\Entities\\';

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
     * Unique validation constraints.
     * 
     * @var array
     */
    private array $uniqueConstraints = [];

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
            foreach ($fieldRules as $rule) {
                $ruleParts = explode(':', $rule);
                $ruleName = $ruleParts[0];

                match ($ruleName) {
                    'min' => $this->applyMinRule(
                        $validator,
                        $field,
                        $ruleParts[1]
                    ),
                    'max' => $this->applyMaxRule(
                        $validator,
                        $field,
                        $ruleParts[1]
                    ),
                    'unique' => $this->applyUniqueRule(
                        // $validator,
                        $field,
                        $ruleParts[1]
                    ),
                    'dateFormat' => $this->applyStandardRule(
                        $validator,
                        'dateFormat',
                        $field,
                        $ruleParts[1]
                    ),
                    default => $this->applyStandardRule(
                        $validator,
                        $rule,
                        $field
                    ),
                };
            }
        }

        $this->applyBatchUniqueRules($validator, $data);

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

    private function applyMinRule(
        ValitronValidator $validator,
        string $field,
        string $value
    ): void {
        $validator->rule('lengthMin', $field, $value);
    }

    private function applyMaxRule(
        ValitronValidator $validator,
        string $field,
        string $value
    ): void {
        $validator->rule('lengthMax', $field, $value);
    }

    private function applyStandardRule(
        ValitronValidator $validator,
        string $rule,
        string $field,
        ?string $value = null
    ): void {
        $validator->rule($rule, $field, $value);
    }

    private function applyUniqueRule(
        string $field,
        string $tableName
    ): void {
        $this->uniqueConstraints[] = [
            'field' => $field,
            'table' => $tableName
        ];
    }

    private function applyBatchUniqueRules(ValitronValidator $validator, array $data): void
    {
        foreach ($this->uniqueConstraints as $constraint) {
            $field = $constraint['field'];
            $table = $constraint['table'];
            $repository = $this->entityManager->getRepository($this->namespace . $this->classifyTableName($table));

            if ($repository->count([$field => $data[$field]])) {
                $validator->error($field, "{field} is already in use.");
            }
        }
    }

    private function classifyTableName(string $tableName): string
    {
        if (is_null($this->inflector)) {
            $this->inflector = InflectorFactory::create()->build();
        }
        return $this->inflector->classify($this->inflector->singularize($tableName));
    }

    public function setValidationEntityManager(EntityManagerInterface $entityManager): self
    {
        $this->entityManager = $entityManager;

        return $this;
    }
}
