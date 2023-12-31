<?php

declare(strict_types=1);

namespace Denosys\Core\Database\Repository;

use Doctrine\ORM\EntityManagerInterface;
use LogicException;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Optional EntityRepository base class with a simplified constructor (for autowiring).
 *
 * To use in your class, inject the "registry" service and call
 * the parent constructor. For example:
 *
 * class YourEntityRepository extends ServiceEntityRepository
 * {
 *     public function __construct(ManagerRegistry $registry)
 *     {
 *         parent::__construct($registry, YourEntity::class);
 *     }
 * }
 *
 * @template T of object
 * @template-extends EntityRepository<T>
 */
class ServiceEntityRepository extends EntityRepository implements ServiceEntityRepositoryInterface
{
    /**
     * Constructor for the ServiceEntityRepository class.
     *
     * @param ManagerRegistry $registry The manager registry.
     * @param class-string $entityClass The class name of the entity.
     *
     * @throws LogicException If the entity manager for the given class is not found.
     */
    public function __construct(EntityManagerInterface $entityManager, string $entityClass)
    {
        parent::__construct($entityManager, $entityManager->getClassMetadata($entityClass));
    }

    public function save(object $entity): void
    {
        $this->validateEntityType($entity);

        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();
    }

    public function delete(object $entity): void
    {
        $this->validateEntityType($entity);

        $this->getEntityManager()->remove($entity);
        $this->getEntityManager()->flush();
    }

    /**
     * Validates that the given entity object is of the expected entity type.
     *
     * @param object $entity The entity object to validate.
     *
     * @throws LogicException If the entity is not of the expected type.
     */
    private function validateEntityType(object $entity): void
    {
        $expectedEntity = $this->getEntityName();
        if (!$entity instanceof $expectedEntity) {
            throw new InvalidEntityTypeException(
                sprintf(
                    'Expected an entity of type "%s", but got "%s".',
                    $this->getEntityName(),
                    get_debug_type($entity)
                )
            );
        }
    }
}