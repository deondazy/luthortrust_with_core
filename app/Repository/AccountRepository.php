<?php

declare(strict_types=1);

namespace Denosys\App\Repository;

use Denosys\App\Database\Entities\Account;
use Denosys\App\Database\Entities\User;
use Denosys\Core\Database\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @template-extends ServiceEntityRepository<User>
 */
class AccountRepository extends ServiceEntityRepository
{
    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct($entityManager, Account::class);
    }
}
