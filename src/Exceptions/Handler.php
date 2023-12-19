<?php

declare(strict_types=1);

namespace Denosys\Core\Exceptions;

use Denosys\Core\Application;
use Denosys\Core\Config\ConfigurationInterface;
use Fig\Http\Message\StatusCodeInterface;
use JetBrains\PhpStorm\NoReturn;
use Psr\Log\LoggerInterface;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;

class Handler
{
    private array $statusCode = [
        StatusCodeInterface::STATUS_UNAUTHORIZED,
        StatusCodeInterface::STATUS_FORBIDDEN,
        StatusCodeInterface::STATUS_NOT_FOUND,
        StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR,
    ];

    public function __construct(
        private readonly ConfigurationInterface $config,
        private readonly LoggerInterface $logger
    ) {
        $this->registerErrorHandling();
    }

    private function registerErrorHandling(): void
    {
        if ($this->config->get('app.debug')) {
            $this->registerDebugHandlers();
        } else {
            $this->registerProductionHandlers();
        }
    }

    private function registerDebugHandlers(): void
    {
        $prettyPageHandler = new DenosysErrorPageHandler();
        $prettyPageHandler->setEditor(PrettyPageHandler::EDITOR_PHPSTORM);

        // $contentCharset = '<none>';
        // if (
        //     method_exists($this->request, 'getContentCharset') &&
        //     $this->request->getContentCharset() !== null
        // ) {
        //     $contentCharset = $this->request->getContentCharset();
        // }

        $prettyPageHandler->addDataTable('Denosys Core', [
            'Version'         => Application::VERSION,
            // 'Accept Charset'  => $this->request->getHeader('ACCEPT_CHARSET') ?: '<none>',
            // 'Content Charset' => $contentCharset,
            // 'HTTP Method'     => $this->request->getMethod(),
            // 'Path'            => $this->request->getUri()->getPath(),
            // 'Query String'    => $this->request->getUri()->getQuery() ?: '<none>',
            // 'Base URL'        => (string) $this->request->getUri(),
            // 'Scheme'          => $this->request->getUri()->getScheme(),
            // 'Port'            => $this->request->getUri()->getPort(),
            // 'Host'            => $this->request->getUri()->getHost(),
        ]);

        $prettyPageHandler->blacklist('_ENV', 'APP_KEY');
        $prettyPageHandler->blacklist('_SERVER', 'APP_KEY');

        $whoops = new Run();
        $whoops->pushHandler($prettyPageHandler);
        $whoops->register();

        // Log the error
        $whoops->pushHandler(
            fn ($exception) =>
            $this->logger->error($exception->getMessage(), [
                'file' => $exception->getFile(),
                'line' => $exception->getLine()
            ])
        );
    }

    private function registerProductionHandlers(): void
    {
        // Register custom error handler to use Monolog
        set_error_handler(
            fn ($severity, $message, $file, $line) =>
            $this->handleError($severity, $message, $file, $line)
        );

        // Set exception handler
        set_exception_handler(
            fn ($exception) =>
            $this->handleException($exception)
        );
    }

    private function handleError($severity, $message, $file, $line): void
    {
        if (!(error_reporting() & $severity)) {
            // This error code is not included in error_reporting
            return;
        }

        $this->logger->error($message, ['file' => $file, 'line' => $line]);
    }

    #[NoReturn]
    private function handleException($exception): void
    {
        $this->logger->error($exception->getMessage(), [
            'file' => $exception->getFile(),
            'line' => $exception->getLine()
        ]);

        if (in_array($exception->getCode(), $this->statusCode)) {
            http_response_code($exception->getCode());
            $this->renderErrorPage($exception->getCode());
        } else {
            http_response_code(StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR);
            $this->renderErrorPage(StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR);
        }
    }

    #[NoReturn]
    private function renderErrorPage(int $statusCode): void
    {
        // TODO: Improve error rendering logic
        require __DIR__ . '/views/' . $statusCode . '.twig';
        exit;
    }
}
