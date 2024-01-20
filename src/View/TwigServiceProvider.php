<?php

declare(strict_types=1);

namespace Denosys\Core\View;

use Slim\Views\Twig;
use Twig\Extension\DebugExtension;
use Denosys\Core\Support\ServiceProvider;
use Denosys\Core\Session\SessionInterface;
use Denosys\Core\Support\Twig\EnvExtension;
use Denosys\Core\Support\Twig\UrlExtension;
use Denosys\Core\Support\Twig\ViteExtension;
use Denosys\Core\Support\Twig\AssetExtension;
use Denosys\Core\Support\Twig\FlashExtension;
use Denosys\Core\Support\Twig\CarbonExtension;
use Denosys\Core\Support\Twig\ConfigExtension;
use Denosys\Core\Support\Twig\UserAccessExtension;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class TwigServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->container->set(Twig::class, function () {
            $twig = Twig::create(config('paths.views_dir'), config('views.twig'));

            $this->registerExtensions($twig);

            return $twig;
        });
    }

    private function registerExtensions(Twig $twig): void
    {
        $twig->addExtension(new ViteExtension(
            config('app.url') . '/build',
            config('paths.build_dir') . '/manifest.json',
            config('app.vite_server')
        ));
        $twig->addExtension(new DebugExtension());
        $twig->addExtension(new EnvExtension());
        $twig->addExtension(new UrlExtension());
        $twig->addExtension(new ConfigExtension($this->getApplication()->getConfigurations()));
        $twig->addExtension(new AssetExtension(config('app.url')));
        $twig->addExtension(new FlashExtension($this->container->get(SessionInterface::class)));
        $twig->addExtension(new UserAccessExtension($this->container->get(AuthorizationCheckerInterface::class)));
        $twig->addExtension(new CarbonExtension());
    }
}
