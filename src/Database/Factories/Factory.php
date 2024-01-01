<?php

declare(strict_types=1);

namespace Denosys\Core\Database\Factories;

use Denosys\Core\Support\ServiceProvider;
use Denosys\Core\Support\Str;
use Doctrine\Inflector\Inflector;
use Doctrine\Inflector\InflectorFactory;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Generator;
use Faker\Factory as FakerFactory;

abstract class Factory
{
    /**
     * Faker instance
     *
     * @var Generator
     */
    protected Generator $faker;

    /**
     * Entity manager instance
     *
     * @var EntityManagerInterface
     */
    protected EntityManagerInterface $entityManager;

    /**
     * Default namespace for factories
     *
     * @var string
     */
    public static string $namespace = 'Denosys\\App\\Database\\Factories\\';

    /**
     * Entity class name
     *
     * @var string
     */
    protected string $entity;

    /**
     * Inflector instance
     *
     * @var Inflector|null
     */
    private static ?Inflector $inflector = null;

    /**
     * Setter methods for entity attributes
     *
     * @var array
     */
    private static array $setterMethods = [];

    public function __construct()
    {
        $this->faker = FakerFactory::create();

        if (self::$inflector === null) {
            self::$inflector = InflectorFactory::create()->build();
        }
    }

    public function setEntityManager(EntityManagerInterface $entityManager): void
    {
        $this->entityManager = $entityManager;
    }

    abstract public function definition(): array;

    public function create(array $attributes = [], bool $flush = true)
    {
        $data = array_merge($this->definition(), $attributes);
        $entity = new $this->entity();

        foreach ($data as $attribute => $value) {
            $setter = $this->getSetter($attribute);

            if (method_exists($entity, $setter)) {
                $entity->$setter($value);
            }
        }

        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }

        return $entity;
    }

    public function createMany(int $number, array $attributes = [], int $batchSize = 20): array
    {
        $entities = [];
        for ($i = 0; $i < $number; $i++) {
            $entity = $this->create($attributes, false); // Add a second parameter to 'create' method
            $entities[] = $entity;

            if (($i + 1) % $batchSize === 0) {
                // Flush and clear the EntityManager every $batchSize iterations
                $this->getEntityManager()->flush();
                $this->getEntityManager()->clear(); // Clear to detach entities and avoid memory leak
            }
        }

        // Flush remaining entities that didn't complete the last batch
        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();

        return $entities;
    }

    public static function factoryForEntity(string $entity): Factory
    {
        $factoryClass = static::resolveFactoryClass($entity);
        return new $factoryClass();
    }

    private function getSetter(string $attribute): string
    {
        if (!isset(self::$setterMethods[$attribute])) {
            $classifiedAttribute = self::$inflector->classify($attribute);
            self::$setterMethods[$attribute] = 'set' . $classifiedAttribute;
        }
        return self::$setterMethods[$attribute];
    }

    private static function resolveFactoryClass(string $entity): string
    {
        $resolver = function (string $entity) {
            $appNamespace = ServiceProvider::getApplication()->getNamespace();

            $entityName = Str::startsWith($entity, $appNamespace . 'Database\\Entities\\')
                ? Str::after($entity, $appNamespace . 'Database\\Entities\\')
                : Str::after($entity, $appNamespace);

            return static::$namespace . $entityName . 'Factory';
        };

        return $resolver($entity);
    }

    public function getEntityManager(): EntityManagerInterface
    {
        return ServiceProvider::getApplication()->getContainer()->get(EntityManagerInterface::class);
    }
}
