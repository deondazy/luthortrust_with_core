<?php

declare(strict_types=1);

namespace Denosys\Core\Support\Twig;

use Denosys\Core\Security\Authorization;
use Psr\Container\ContainerInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class UserAccessExtension extends AbstractExtension
{
    public function __construct(private readonly ?AuthorizationCheckerInterface $securityChecker = null)
    {
    }

    public function isGranted(mixed $role, mixed $object = null): bool
    {
        if (null === $this->securityChecker) {
            return false;
        }

        try {
            return $this->securityChecker->isGranted($role, $object);
        } catch (AuthenticationCredentialsNotFoundException) {
            return false;
        }
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('is_granted', $this->isGranted(...)),
        ];
    }
}
