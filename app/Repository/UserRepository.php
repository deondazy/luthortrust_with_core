<?php

declare(strict_types=1);

namespace Denosys\App\Repository;

use DateTime;
use Denosys\Core\Pagination\Paginator;
use Denosys\App\Database\Entities\User;
use Denosys\Core\Database\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @template-extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct($entityManager, User::class);
    }

    public function findLatest(int $page = 1): Paginator
    {
        $queryBuilder = $this->createQueryBuilder('user')
            ->where('user.createdAt <= :now')
            ->orderBy('user.createdAt', 'DESC')
            ->setParameter('now', new DateTime())
        ;

        return (new Paginator($queryBuilder))->paginate($page);
    }
}
