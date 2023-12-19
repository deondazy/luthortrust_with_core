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
    public function __construct(EntityManagerInterface $entityManger, string $entityClass)
    {
        parent::__construct($entityManger, $entityManger->getClassMetadata($entityClass));
    }
}