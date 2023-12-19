<?php

declare(strict_types=1);

namespace Denosys\Core\FormValidation;

use Doctrine\Inflector\InflectorFactory;
use Doctrine\ORM\EntityManagerInterface;
use Valitron\Validator;

class FormValidation
{
    private EntityManagerInterface $entityManager;

    public function validate(array $data, array $rules): bool
    {
        $validator = new Validator($data);

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
                        $validator,
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

        if ($validator->validate()) {
            return true;
        }

        throw new FormValidationException($validator->errors());
    }

    private function applyMinRule(
        Validator $validator,
        string $field,
        string $value
    ): void {
        $validator->rule('lengthMin', $field, $value);
    }

    private function applyMaxRule(
        Validator $validator,
        string $field,
        string $value
    ): void {
        $validator->rule('lengthMax', $field, $value);
    }

    private function applyStandardRule(
        Validator $validator,
        string $rule,
        string $field,
        ?string $value = null
    ): void {
        $validator->rule($rule, $field, $value);
    }

    private function applyUniqueRule(
        Validator $validator,
        string $field,
        string $pluralSnakeCaseTableName
    ): void {
        $inflector = InflectorFactory::create()->build();

        $singularSnakeCaseTableName = $inflector
            ->singularize($pluralSnakeCaseTableName);
        $classCaseName = $inflector
            ->classify($singularSnakeCaseTableName);

        // TODO: Find a better way to get the entities namespace.
        $className = "Denosys\\App\\Database\\Entities\\" . $classCaseName;

        $validator->rule(function (
            string $field,
            string $value,
            array $params,
            array $fields
        ) use ($className) {
            return !$this->entityManager
                ->getRepository($className)
                ->count([$field => $value]);
        }, $field)->message('{field} is already in use.');
    }

    public function setValidationEntityManager(
        EntityManagerInterface $entityManager
    ): self {
        $this->entityManager = $entityManager;

        return $this;
    }
}
