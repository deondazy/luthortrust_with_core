<?php

declare(strict_types=1);

namespace Denosys\App\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Denosys\App\Database\Entities\Country;
use Denosys\Core\Database\Repository\ServiceEntityRepository;

/**
 * @template-extends ServiceEntityRepository<Country>
 */
class CountryRepository extends ServiceEntityRepository
{
    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct($entityManager, Country::class);
    }
}