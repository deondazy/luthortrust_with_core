<?php

declare(strict_types=1);

return [
    Slim\Views\TwigMiddleware::class,
    Denosys\App\Middleware\SetFormValidationExceptionMiddleware::class,
    Denosys\App\Middleware\GetOldFormDataMiddleware::class,
    Denosys\App\Middleware\GetFormValidationExceptionMiddleware::class,
    Denosys\App\Middleware\SessionEncryptMiddleware::class,
    Denosys\App\Middleware\SessionStartMiddleware::class,
];
