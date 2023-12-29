<?php

declare(strict_types=1);

namespace Denosys\Core\Support\Clockwork;

use Slim\App;
use Clockwork\Clockwork;
use Clockwork\Storage\FileStorage;
use Doctrine\ORM\EntityManagerInterface;
use Denosys\Core\Support\ServiceProvider;
use Clockwork\DataSource\DoctrineDataSource;
use Clockwork\Support\Slim\ClockworkMiddleware;

class ClockworkServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->container->set(Clockwork::class, function () {
            $clockwork = new Clockwork();

            $clockwork->storage(new FileStorage(config('paths.storage_dir') . '/clockwork'));
            $clockwork->addDataSource(new DoctrineDataSource($this->container->get(EntityManagerInterface::class)));

            return $clockwork;
        });

        // if ($this->getApplication()->isLocal()) {
            $this->registerClockworkMiddleware();
        // }
    }

    private function registerClockworkMiddleware(): void
    {
        $this->container->set(ClockworkMiddleware::class, function () {
            return new ClockworkMiddleware(
                $this->container->get(App::class),
                $this->container->get(Clockwork::class)
            );
        });
    }
}