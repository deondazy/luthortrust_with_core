<?php

declare(strict_types=1);

namespace Denosys\Core\Validation\Rules;

use Doctrine\Inflector\Inflector;
use Doctrine\Inflector\InflectorFactory;
use Doctrine\ORM\EntityManagerInterface;
use Valitron\Validator as ValitronValidator;
use Denosys\Core\Validation\ValidationException;

class UniqueRule implements RuleInterface
{
    /**
     * Default namespace for Entities
     *
     * @var string
     */
    private string $namespace = 'Denosys\\App\\Database\\Entities\\';

    /**
     * Unique validation constraints.
     * 
     * @var array
     */
    private array $uniqueConstraints = [];

    /**
     * Inflector instance
     *
     * @var Inflector|null
     */
    private ?Inflector $inflector = null;

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly string $tableName,
        private readonly array $data
    ) {
    }

    public function apply(ValitronValidator $validator, string $rule, string|array $field): void
    {
        $this->uniqueConstraints[] = [
            'field' => $field,
            'table' => $this->tableName
        ];

        $this->applyBatchUniqueRules($validator, $this->data);
    }

    private function applyBatchUniqueRules(ValitronValidator $validator, array $data): void
    {
        foreach ($this->uniqueConstraints as $constraint) {
            $field = $constraint['field'];
            $table = $constraint['table'];
            $repository = $this->entityManager->getRepository($this->namespace . $this->classifyTableName($table));

            if ($repository === null) {
                throw new ValidationException([
                    'table' => "Table $table does not exist."
                ]);
            }

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
}
