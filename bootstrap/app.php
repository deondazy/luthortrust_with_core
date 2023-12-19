<?php

/**
 * The Denosys Core Bootstrap File
 *
 * @package Denosys\Core
 * @version 1.0.0
 */

require __DIR__ . '/../vendor/autoload.php';

return (new Denosys\Core\Application(
    $_ENV['APP_BASE_PATH'] ?? dirname(__DIR__)
));
