<?php

declare(strict_types=1);

namespace Denosys\App\Services\User;

use Carbon\Carbon;
use Denosys\App\Database\Entities\User;
use Denosys\App\DTO\UserDTO;
use Denosys\App\Repository\CountryRepository;
use Denosys\App\Repository\UserRepository;
use Denosys\Core\Security\CurrentUser;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserCreationService
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly CountryRepository $countryRepository,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly CurrentUser $currentUser,
    ) {
    }

    public function createUser(UserDTO $data): void
    {
        $user = new User();

        if ($data->passportPhoto) {
            $user->updateProfilePhoto($data->passportPhoto);
        }

        $user
            ->setUsername($data->username)
            ->setEmail($data->email)
            ->setMobileNumber($data->mobileNumber)
            ->setFirstName($data->firstName)
            ->setMiddleName($data->middleName)
            ->setLastName($data->lastName)
            ->setGender($data->gender)
            ->setDateOfBirth(Carbon::createFromFormat('Y-m-d', $data->dateOfBirth))
            ->setCountry($this->countryRepository->find($data->country))
            ->setState($data->state)
            ->setCity($data->city)
            ->setAddress($data->address)
            ->setRequireCot((bool) $data->requireCot)
            ->setRequireImf((bool) $data->requireImf)
            ->setRequireTax((bool) $data->requireTax)
            ->setStatus(User::STATUS_ACTIVE)
            ->setCreatedBy($this->currentUser->getUser());

        $hashedPassword = $this->passwordHasher
            ->hashPassword($user, $data->password);
        $user->setPassword($hashedPassword)
            ->setRoles(['ROLE_USER']);

        $this->userRepository->save($user);
    }
}
