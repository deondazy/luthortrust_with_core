<?php

declare(strict_types=1);

namespace Denosys\Core\Session;

use Throwable;
use LogicException;

class SessionNotFoundException extends LogicException
{
    public function __construct(string $message = 'There is no session available.', int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
