<?php

declare(strict_types=1);

namespace Denosys\App\Services;

use DateTime;
use Denosys\App\Database\Entities\Country;
use Denosys\App\Database\Entities\User;
use Denosys\App\DataObjects\UserData;
use Denosys\Core\Security\CurrentUser;
use Doctrine\ORM\EntityManagerInterface;
use Denosys\Core\FormValidation\FormValidation;
use Psr\Http\Message\ServerRequestInterface as Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authentication\AuthenticationTrustResolverInterface;

class UserAuthenticationService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly TokenStorageInterface $tokenStorage,
        private readonly AuthenticationTrustResolverInterface $authChecker,
        private readonly ?CurrentUser $currentUser = null
    ) {
    }

    public function newUser(Request $request): void
    {
        $data = $this->getUserData($request);

        $rules = [
            'username'      => ['required', 'alphaNum', 'min:3', 'max:50', 'unique:users'],
            'password'      => ['required', 'min:8'],
            'email'         => ['required', 'email', 'unique:users'],
            'mobileNumber'  => ['required', 'unique:users'],
            'firstName'     => ['required'],
            'middleName'    => ['optional'],
            'lastName'      => ['required'],
            'gender'        => ['required'],
            'dateOfBirth'   => ['required', 'dateFormat:Y-m-d'],
            'country'       => ['required'],
            'state'         => ['required'],
            'city'          => ['required'],
            'address'       => ['required'],
            'passportPhoto' => ['optional'],
            'requireCot'    => ['optional'],
            'requireImf'    => ['optional'],
            'requireTax'    => ['optional'],
        ];

        (new FormValidation())
            ->setValidationEntityManager($this->entityManager)
            ->validate($data->toArray(), $rules);

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
            ->setCountry($this->entityManager->getReference(Country::class, $data->country))
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

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    public function login(array $credentials): void
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy([
            'username' => $credentials['UserNameInput']
        ]);

        if (!$user instanceof UserInterface) {
            throw new AuthenticationException('Invalid credentials.');
        }

        if (!$this->passwordHasher->isPasswordValid($user, $credentials['PasswordInput'])) {
            throw new AuthenticationException('Invalid credentials.');
        }

        $token = new UsernamePasswordToken($user, 'main', $user->getRoles());

        if (!$this->authChecker->isAuthenticated($token)) {
            throw new AuthenticationException('Access Denied.');
        }

        $this->tokenStorage->setToken($token);
    }

    public function logout(): void
    {
        $this->tokenStorage->setToken(null);
    }

    public function updateUser(User $client, Request $request): void
    {
        $data = $this->getUserData($request);

        $fields = [
            'email'        => 'setEmail',
            'username'     => 'setUsername',
            'mobileNumber' => 'setMobileNumber',
            'firstName'    => 'setFirstName',
            'middleName'   => 'setMiddleName',
            'lastName'     => 'setLastName',
            'gender'       => 'setGender',
            'dateOfBirth'  => 'setDateOfBirth',
            'country'      => 'setCountry',
            'state'        => 'setState',
            'city'         => 'setCity',
            'address'      => 'setAddress',
            'requireCot'   => 'setRequireCot',
            'requireImf'   => 'setRequireImf',
            'requireTax'   => 'setRequireTax',
        ];

        foreach ($fields as $field => $setter) {
            $getter = 'get' . ucfirst($field);
            if ($client->$getter() !== $data->$field) {
                if ($field === 'dateOfBirth' && !is_null($data->$field)) {
                    $value = new DateTime($data->$field);
                } elseif ($field === 'country') {
                    $value = $this->entityManager->getRepository(Country::class)->find($data->$field);
                } elseif ($field === 'requireCot') {
                    $value = filter_var($data->$field, FILTER_VALIDATE_BOOLEAN);
                } elseif ($field === 'requireTax') {
                    $value = filter_var($data->$field, FILTER_VALIDATE_BOOLEAN);
                } elseif ($field === 'requireImf') {
                    $value = filter_var($data->$field, FILTER_VALIDATE_BOOLEAN);
                } else {
                    $value = $data->$field;
                }

                $client->$setter($value);
            }
        }

        $this->entityManager->persist($client);
        $this->entityManager->flush();
    }

    public function delete(User $user): void
    {
        $this->entityManager->remove($user);
        $this->entityManager->flush();
    }

    private function getUserData(Request $request): UserData
    {
        $formData = $request->getParsedBody();

        return new UserData(
            email: $formData['email'] ?? null,
            username: $formData['username'] ?? null,
            password: $formData['password'] ?? null,
            mobileNumber: $formData['mobileNumber'] ?? null,
            firstName: $formData['firstName'] ?? null,
            middleName: $formData['middleName'] ?? null,
            lastName: $formData['lastName'] ?? null,
            gender: $formData['gender'] ?? null,
            dateOfBirth: $formData['dateOfBirth'] ?? null,
            address: $formData['address'] ?? null,
            city: $formData['city'] ?? null,
            state: $formData['state'] ?? null,
            country: $formData['country'] ?? null,
            passportPhoto: $formData['passportPhoto'] ?? null,
            requireCot: isset($formData['requireCot']) ? true : false,
            requireImf: isset($formData['requireImf']) ? true : false,
            requireTax: isset($formData['requireTax']) ? true : false,
        );
    }
}
