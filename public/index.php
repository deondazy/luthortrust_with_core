<?php

declare(strict_types=1);

error_reporting(E_ALL);
ini_set('display_errors', '1');

$app = require_once __DIR__ . '/../bootstrap/app.php';

// Register exception handler
$app->registerExceptionHandler();

// Register middleware
$app->registerMiddleware();

$app->run();
