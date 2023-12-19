<?php

declare(strict_types=1);

namespace Denosys\Core\Session;

use Denosys\Core\Support\ServiceProvider;

class SessionServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->registerSession();
        $this->registerSessionManager();
    }

    protected function registerSession(): void
    {
        $this->container->set(SessionInterface::class, function () {
            return new NativeSession($this->container);
        });
    }

    protected function registerSessionManager(): void
    {
        $this->container->set(SessionManagerInterface::class, function () {
            return $this->container->get(SessionInterface::class);
        });
    }
}
