<?php

declare(strict_types=1);

namespace Denosys\Core;

use Slim\App;
use Exception;
use RuntimeException;
use Denosys\Core\Controller\Bridge;
use Denosys\Core\Exceptions\Handler;
use Psr\Container\ContainerInterface;
use Denosys\Core\Support\ServiceProvider;
use Denosys\Core\Container\ContainerFactory;
use Denosys\Core\Config\ConfigurationManager;
use Denosys\Core\Config\ArrayFileConfiguration;
use Denosys\Core\Config\ConfigurationInterface;
use Denosys\Core\Environment\EnvironmentLoader;
use Denosys\Core\Http\ServerRequestCreatorFactory;

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
    protected $slimApp;

    /**
     * The container instance.
     *
     * @var ContainerInterface
     */
    protected static $container;

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
     * @param string|null $basePath The base path of the application.
     *
     * @throws Exception
     */
    public function __construct(protected ?string $basePath = null)
    {
        EnvironmentLoader::load($this->basePath());
        $this->registerExceptionHandler();
        $this->buildContainer();
        $this->loadBaseBindings();
        $this->getSlimApp();
        $this->registerContainerAliases();
        ServiceProvider::setApplication($this);
    }

    /**
     * Build and configure the DI container.
     *
     * @throws Exception
     */
    private function buildContainer(): void
    {
        if (static::$container !== null) {
            return;
        }

        static::$container = ContainerFactory::build(
            $this->basePath('/bootstrap/container.php'),
            $this->basePath('storage/cache/container'),
            $this->isProduction()
        );
    }

    public function getConfigurations(): ConfigurationInterface
    {
        $configurationManager = (new ConfigurationManager())
            ->loadConfigurationFiles($this->basePath('config/'));

        return new ArrayFileConfiguration($configurationManager);
    }

    public static function getContainer(): ContainerInterface
    {
        return static::$container;
    }

    public function getSlimApp(): App
    {
        if (!isset($this->slimApp)) {
            $slimApp = Bridge::create(static::$container);
            $slimApp->addRoutingMiddleware();
            $this->slimApp = $slimApp;
        }

        return $this->slimApp;
    }

    protected function loadBaseBindings(): void
    {
        static::$container->set(ConfigurationInterface::class, $this->getConfigurations());
    }

    public function registerMiddleware(): void
    {
        $middleware = require $this->basePath('config/middleware.php');

        foreach ($middleware as $middlewareClass) {
            $this->getSlimApp()->add(static::$container->get($middlewareClass));
        }
    }

    public function registerExceptionHandler(): Handler
    {
        return new Handler($this->getConfigurations());
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
        return EnvironmentLoader::get('APP_ENV', 'production') === 'production';
    }

    public function isLocal(): bool
    {
        return EnvironmentLoader::get('APP_ENV', 'production') === 'local';
    }

    public function run(): void
    {
        $serverRequestCreator = ServerRequestCreatorFactory::create();
        $request = $serverRequestCreator->createServerRequestFromGlobals();

        $this->slimApp->run($request);
    }

    protected function registerContainerAliases(): void
    {
        $aliases = [
            'app' => App::class,
            'config' => ConfigurationInterface::class,
        ];

        foreach ($aliases as $key => $alias) {
            static::$container->set($key, static::$container->get($alias));
        }
    }
}
