<?php

declare(strict_types=1);

namespace Denosys\App\Services;

use Denosys\App\Database\Entities\User;
use Denosys\App\Repository\UserRepository;
use Denosys\Core\Security\CurrentUser;
use Denosys\Core\Validation\ValidationException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\User\UserInterface;

class UserAuthenticationService
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly TokenStorageInterface $tokenStorage,
    ) {
    }

    public function login(array $credentials): void
    {
        $user = $this->userRepository->findOneBy([
            'username' => $credentials['username']
        ]);

        if (!$user instanceof UserInterface) {
            throw new ValidationException(['username' => 'These credentials do not match our records.']);
        }

        if (!$this->passwordHasher->isPasswordValid($user, $credentials['password'])) {
            throw new ValidationException(['username' => 'These credentials do not match our records.']);
        }

        $token = new UsernamePasswordToken($user, 'main', $user->getRoles());

        $this->tokenStorage->setToken($token);
    }

    public function logout(): void
    {
        $this->tokenStorage->setToken(null);
    }
}
