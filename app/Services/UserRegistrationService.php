<?php

declare(strict_types=1);

namespace Denosys\App\Services;

use DateTime;
use Denosys\App\DTO\UserDTO;
use Denosys\Core\Security\CurrentUser;
use Denosys\App\Database\Entities\User;
use Denosys\App\Repository\UserRepository;
use Denosys\App\Repository\CountryRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserRegistrationService
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly CountryRepository $countryRepository,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly ?CurrentUser $currentUser = null
    ) {
    }

    public function register(UserDTO $data): void
    {
        $user = new User();

        $user
            ->setUsername($data->username)
            ->setEmail($data->email)
            ->setMobileNumber($data->mobileNumber)
            ->setFirstName($data->firstName)
            ->setMiddleName($data->middleName)
            ->setLastName($data->lastName)
            ->setGender($data->gender)
            ->setDateOfBirth(new DateTime($data->dateOfBirth))
            ->setCountry($this->countryRepository->find($data->country))
            ->setState($data->state)
            ->setCity($data->city)
            ->setAddress($data->address)
            ->setPassport($data->passportPhoto)
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
