<?php

declare(strict_types=1);

use Denosys\Core\Application;
use Denosys\Core\Commands\DatabaseSeedCommand;
use Denosys\Core\Commands\GenerateEncryptionKeyCommand;
use Denosys\Core\Config\ConfigurationInterface;

return fn (ConfigurationInterface $config, Application $core) => [
    new GenerateEncryptionKeyCommand($config),
    new DatabaseSeedCommand($config, $core),
];
