<?php

declare(strict_types=1);

namespace Denosys\Core\Database\Factories;

use Doctrine\ORM\EntityManagerInterface;

trait HasFactory
{
    protected static ?EntityManagerInterface $entityManager = null;

    public static function setEntityManager(EntityManagerInterface $entityManager): void
    {
        self::$entityManager = $entityManager;
    }

    /**
     * Get a new factory instance for the entity.
     *
     * @return Factory
     */
    public static function factory(): Factory
    {
        return Factory::factoryForEntity(get_called_class());
    }
}
