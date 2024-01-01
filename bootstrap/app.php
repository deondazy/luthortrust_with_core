<?php

/**
 * The Denosys Core Bootstrap File
 */

require __DIR__ . '/../vendor/autoload.php';

return (new Denosys\Core\Application(
    $_ENV['APP_BASE_PATH'] ?? dirname(__DIR__)
));
