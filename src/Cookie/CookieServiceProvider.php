<?php

declare(strict_types=1);

namespace Denosys\Core\Cookie;

use Denosys\Core\Support\ServiceProvider;
use Slim\Psr7\Cookies;

class CookieServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->container->set('cookie', function () {
            $config = $this->getApplication()->getConfigurations()->get('session');
            
            return (new Cookies($_COOKIE))->setDefaults([
                'domain'   => $config['domain'],
                'path'     => $config['path'],
                'expires'  => $config['lifetime'],
                'secure'   => $config['secure'],
                'httponly' => $config['httponly'],
                'samesite' => $config['samesite'] ?? null,
            ]);
        });
    }
}
