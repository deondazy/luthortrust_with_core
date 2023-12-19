<?php

declare(strict_types=1);

namespace Denosys\Core;

use Denosys\Core\Config\ArrayFileConfiguration;
use Denosys\Core\Config\ConfigurationInterface;
use Denosys\Core\Config\ConfigurationManager;
use Denosys\Core\Container\ContainerFactory;
use Denosys\Core\Controller\Bridge;
use Denosys\Core\Environment\EnvironmentLoader;
use Denosys\Core\Environment\EnvironmentLoaderInterface;
use Denosys\Core\Exceptions\Handler;
use Denosys\Core\Support\ServiceProvider;
use Dotenv\Repository\Adapter\PutenvAdapter;
use Dotenv\Repository\RepositoryBuilder;
use Exception;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Slim\App;
use Slim\Views\Twig;

class Application
{
    /**
     * Current version
     *
     * @var string
     */
    public const VERSION = '0.0.1';

    /**
     * The Slim application instance.
     *
     * @var App
     */
    protected App $slimApp;

    /**
     * The container instance.
     *
     * @var ContainerInterface
     */
    protected ContainerInterface $container;

    /**
     * The application's environment file.
     *
     * @var string
     */
    protected string $environmentFile = '.env';

    /**
     * The application's namespace.
     *
     * @var string|null
     */
    protected ?string $namespace = null;

    /**
     * Create the application instance.
     *
     * @param string $basePath The base path of the application.
     *
     * @throws Exception
     */
    public function __construct(protected string $basePath)
    {
        $this->environmentLoader()->load($this->basePath());
        $this->container = $this->buildContainer();
        $this->loadBaseBindings();
        ServiceProvider::setApplication($this);
        $this->getSlimApp();
        $this->registerContainerAliases();
    }

    /**
     * Build and configure the DI container.
     *
     * @throws Exception
     */
    private function buildContainer(): ContainerInterface
    {
        return ContainerFactory::build(
            $this->basePath('/bootstrap/container.php'),
            $this->basePath('storage/cache/container'),
            $this->isProduction()
        );
    }

    protected function environmentLoader(): EnvironmentLoaderInterface
    {
        $builder = RepositoryBuilder::createWithDefaultAdapters();
        $builder = $builder->addAdapter(PutenvAdapter::class);
        $repository = $builder->immutable()->make();

        return new EnvironmentLoader($repository);
    }

    public function getConfigurations(): ConfigurationInterface
    {
        $configurationManager = (new ConfigurationManager())
            ->loadConfigurationFiles($this->basePath('config/'));

        $configurations = new ArrayFileConfiguration($configurationManager);

        $this->container->set(ConfigurationInterface::class, $configurations);

        return $configurations;
    }

    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }

    public function getSlimApp(): App
    {
        if (!isset($this->slimApp)) {
            $slimApp = Bridge::create($this->container);
            $slimApp->addRoutingMiddleware();
            $this->slimApp = $slimApp;
        }

        return $this->slimApp;
    }

    protected function loadBaseBindings(): void
    {
        $this->container->set(EnvironmentLoaderInterface::class, $this->environmentLoader());
        $this->container->set(ConfigurationInterface::class, $this->getConfigurations());
    }

    public function registerMiddleware(): void
    {
        $middleware = require $this->basePath('config/middleware.php');

        foreach ($middleware as $middlewareClass) {
            $this->getSlimApp()->add($this->container->get($middlewareClass));
        }
    }

    public function registerExceptionHandler(): Handler
    {
        return new Handler(
            $this->container->get(ConfigurationInterface::class),
            $this->container->get(LoggerInterface::class)
        );
    }

    /**
     * Get the path to the application "app" directory.
     *
     * @param string $path The path to join with the app path.
     *
     * @return string The joined path.
     */
    public function path(string $path = ''): string
    {
        return $this->joinPaths($this->basePath('app'), $path);
    }

    /**
     * Get the base path of the application.
     *
     * @param string $path The path to join with the base path.
     *
     * @return string The joined path.
     */
    public function basePath(string $path = ''): string
    {
        return $this->joinPaths($this->basePath, $path);
    }

    /**
     * Join the base path with a given path.
     *
     * @param string $basePath The base path of the application.
     * @param string $path The path to join with the base path.
     *
     * @return string The joined path.
     */
    public function joinPaths(string $basePath, string $path = ''): string
    {
        return $basePath . ($path != '' ? DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR) : '');
    }

    public function version(): string
    {
        return self::VERSION;
    }

    /**
     * Get the application namespace.
     *
     * @return string
     *
     * @throws RuntimeException
     */
    public function getNamespace(): string
    {
        if (!is_null($this->namespace)) {
            return $this->namespace;
        }

        $composer = json_decode(file_get_contents($this->basePath('composer.json')), true);

        foreach ((array) $composer['autoload']['psr-4'] as $namespace => $path) {
            foreach ((array) $path as $pathChoice) {
                if (realpath($this->path()) === realpath($this->basePath($pathChoice))) {
                    return $this->namespace = $namespace;
                }
            }
        }

        throw new RuntimeException('Unable to detect application namespace.');
    }

    public function getEnvironmentFile(): string
    {
        return $this->environmentFile;
    }

    public function isProduction(): bool
    {
        return $this->environmentLoader()->get('APP_ENV', 'production') === 'production';
    }

    public function isLocal(): bool
    {
        return $this->environmentLoader()->get('APP_ENV', 'production') === 'local';
    }

    public function run(): void
    {
        $this->slimApp->run();
    }

    protected function registerContainerAliases(): void
    {
        $aliases = [
            'app' => App::class,
            'config' => ConfigurationInterface::class,
            'view' => Twig::class,
        ];

        foreach ($aliases as $key => $alias) {
            $this->container->set($key, $this->container->get($alias));
        }
    }
}
