<?php

declare(strict_types=1);

use Denosys\Core\Log\LogServiceProvider;
use Denosys\Core\View\TwigServiceProvider;
use Denosys\Core\Routing\RoutingServiceProvider;
use Denosys\Core\Session\SessionServiceProvider;
use Denosys\Core\Database\DatabaseServiceProvider;
use Denosys\Core\Support\Clockwork\ClockworkServiceProvider;

return [
    DatabaseServiceProvider::class,
    SessionServiceProvider::class,
    RoutingServiceProvider::class,
    TwigServiceProvider::class,
    LogServiceProvider::class,
    ClockworkServiceProvider::class,
];
