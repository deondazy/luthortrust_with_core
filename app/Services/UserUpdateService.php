<?php

declare(strict_types=1);

namespace Denosys\App\Services;

use DateTime;
use Denosys\App\Database\Entities\User;
use Denosys\App\DTO\UserDTO;
use Denosys\App\Repository\UserRepository;

class UserUpdateService
{
    public function __construct(private readonly UserRepository $userRepository)
    {
    }

    public function updateUser(User $user, UserDTO $data): void
    {
        if ($user->getEmail() !== $data->email) {
            $user->setEmail($data->email);
        }

        if ($user->getUsername() !== $data->username) {
            $user->setUsername($data->username);
        }

        if ($user->getMobileNumber() !== $data->mobileNumber) {
            $user->setMobileNumber($data->mobileNumber);
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

        if ($user->getCountry()->getId() !== (int) $data->country) {
            $user->setCountry($data->country);
        }

        if ($user->getState() !== $data->state) {
            $user->setState($data->state);
        }

        if ($user->getCity() !== $data->city) {
            $user->setCity($data->city);
        }

        if ($user->getAddress() !== $data->address) {
            $user->setAddress($data->address);
        }

        if ($user->getRequireCot() !== $data->requireCot) {
            $user->setRequireCot($data->requireCot);
        }

        if ($user->getRequireImf() !== $data->requireImf) {
            $user->setRequireImf($data->requireImf);
        }

        if ($user->getRequireTax() !== $data->requireTax) {
            $user->setRequireTax($data->requireTax);
        }

        $this->userRepository->save($user);
    }
}