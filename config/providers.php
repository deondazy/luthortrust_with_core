<?php

declare(strict_types=1);

use Denosys\Core\Log\LogServiceProvider;
use Denosys\Core\View\TwigServiceProvider;
use Denosys\Core\Routing\RoutingServiceProvider;
use Denosys\Core\Session\SessionServiceProvider;
use Denosys\Core\Database\DatabaseServiceProvider;
use Denosys\Core\Filesystem\FilesystemServiceProvider;
use Denosys\Core\Validation\ValidationServiceProvider;
use Denosys\Core\Support\Clockwork\ClockworkServiceProvider;

return [
    LogServiceProvider::class,
    TwigServiceProvider::class,
    SessionServiceProvider::class,
    RoutingServiceProvider::class,
    DatabaseServiceProvider::class,
    ClockworkServiceProvider::class,
    FilesystemServiceProvider::class,
    ValidationServiceProvider::class,
];
