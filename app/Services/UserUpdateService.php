<?php

declare(strict_types=1);

namespace Denosys\App\Services;

use DateTime;
use Denosys\App\DTO\UserDTO;
use Denosys\App\Repository\UserRepository;

class UserUpdateService
{
    public function __construct(private readonly UserRepository $userRepository)
    {
    }

    public function updateUser(int $userId, UserDTO $data): void
    {
        $user = $this->userRepository->find($userId);

        if (!$user) {
            throw new \Exception('User not found.');
        }

        if ($user->getEmail() !== $data->email) {
            $user->setEmail($data->email);
        }

        if ($user->getUsername() !== $data->username) {
            $user->setUsername($data->username);
        }

        if ($user->getFirstName() !== $data->firstName) {
            $user->setFirstName($data->firstName);
        }

        if ($user->getMiddleName() !== $data->middleName) {
            $user->setMiddleName($data->middleName);
        }

        if ($user->getLastName() !== $data->lastName) {
            $user->setLastName($data->lastName);
        }

        if ($user->getGender() !== $data->gender) {
            $user->setGender($data->gender);
        }

        if ($user->getDateOfBirth() !== $data->dateOfBirth) {
            $user->setDateOfBirth(new DateTime($data->dateOfBirth));
        }

        if ($user->getAddress() !== $data->address) {
            $user->setAddress($data->address);
        }

        if ($user->getCity() !== $data->city) {
            $user->setCity($data->city);
        }

        if ($user->getCountry() !== $data->country) {
            $user->setCountry($data->country);
        }

    }
}