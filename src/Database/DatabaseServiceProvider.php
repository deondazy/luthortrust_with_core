<?php

declare(strict_types=1);

namespace Denosys\Core\Database;

use Denosys\Core\Support\ServiceProvider;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMSetup;

class DatabaseServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->container->set(EntityManagerInterface::class, function () {
            $config = $this->getApplication()->getConfigurations();
            $defaultConnection = $config->get('database.default');
            $configParams = $config->get('database.connections.'. $defaultConnection);

            return new EntityManager(
                DriverManager::getConnection($configParams),
                ORMSetup::createAttributeMetadataConfiguration(
                    $config->get('paths.entity_dir'),
                    $config->get('app.debug')
                )
            );
        });
    }
}
