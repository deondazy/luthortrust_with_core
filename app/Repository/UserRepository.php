<?php

declare(strict_types=1);

namespace Denosys\App\Repository;

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
            ->select('user', 'country')
            ->leftJoin('user.country', 'country')
            ->orderBy('user.createdAt', 'DESC');

        return (new Paginator($queryBuilder))->paginate($page);
    }
}
