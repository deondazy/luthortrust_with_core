<?php

declare(strict_types=1);

namespace Denosys\App\Services;

use Carbon\Carbon;
use Denosys\App\DTO\UserDTO;
use Denosys\App\Database\Entities\User;
use Denosys\App\Repository\UserRepository;
use Denosys\App\Repository\CountryRepository;

class UserUpdateService
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly CountryRepository $countryRepository,
    ) {
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

        if (Carbon::make($user->getDateOfBirth())->format('Y-m-d') !== $data->dateOfBirth) {
            $user->setDateOfBirth(Carbon::createFromFormat('Y-m-d', $data->dateOfBirth));
        }

        if ($user->getCountry()->getId() !== (int) $data->country) {
            $user->setCountry($this->countryRepository->find($data->country));
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