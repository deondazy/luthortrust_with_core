<?php

declare(strict_types=1);

namespace Denosys\App\Services\User;

use Denosys\App\Database\Entities\User;
use Denosys\App\Repository\UserRepository;

class UserDeleteService
{
    public function __construct(private readonly UserRepository $userRepository)
    {
    }

    public function deleteUser(User $user): void
    {
        $user->deleteProfilePhoto();
        $this->userRepository->delete($user);
    }
}
