<?php

declare(strict_types=1);

namespace Denosys\Core\Log;

use Denosys\Core\Support\ServiceProvider;
use Exception;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

class LogServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->container->set(
            LoggerInterface::class,
            function () {
                $config = $this->getApplication()->getConfigurations();

                $logger = new Logger($config->get('app.env'));

                $fileHandler = new StreamHandler($config->get('paths.log_dir') . '/core.log', Level::Error);

                // Create a custom formatter that includes stack traces
                $output = "[%datetime%] %channel%.%level_name%: %message% %context%\n%extra%\n\n";
                $formatter = new LineFormatter(format: $output, dateFormat: 'Y-m-d H:i:s', allowInlineLineBreaks: true);
                $fileHandler->setFormatter($formatter);

                // Tell the handler to include stack traces in the 'extra' field of records
                $fileHandler->pushProcessor(function ($record) {
                    $record['extra']['stacktrace'] = (new Exception())->getTraceAsString();
                    return $record;
                });

                $logger->pushHandler($fileHandler);

                return $logger;
            }
        );
    }
}
