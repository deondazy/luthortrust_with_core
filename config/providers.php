<?php

declare(strict_types=1);

use Denosys\Core\Database\DatabaseServiceProvider;
use Denosys\Core\Log\LogServiceProvider;
use Denosys\Core\Routing\RoutingServiceProvider;
use Denosys\Core\Session\SessionServiceProvider;
use Denosys\Core\View\TwigServiceProvider;

return [
    DatabaseServiceProvider::class,
    SessionServiceProvider::class,
    RoutingServiceProvider::class,
    TwigServiceProvider::class,
    LogServiceProvider::class,
];
