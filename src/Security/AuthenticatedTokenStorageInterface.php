<?php

declare(strict_types=1);

namespace Denosys\Core\Security;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

interface AuthenticatedTokenStorageInterface extends TokenStorageInterface
{
    public function isTokenAuthenticated(): bool;
}
