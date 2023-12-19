<?php

declare(strict_types=1);

namespace Denosys\Core\Exceptions;

use Whoops\Handler\PrettyPageHandler;

class DenosysErrorPageHandler extends PrettyPageHandler
{
    // public function __construct(
    //     private readonly string $errorPagePath,
    //     private readonly string $errorPageTemplate,
    //     private readonly string $errorPageTemplateKey
    // ) {
    // }

    public function handle(): int|null
    {
        $exception = $this->getInspector()->getException();
        $this->setPageTitle($exception->getMessage() ?: 'Error');

        return parent::handle();
    }
}