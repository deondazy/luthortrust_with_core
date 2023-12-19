<?php

declare(strict_types=1);

namespace Denosys\App\DataObjects;

use Denosys\Core\Support\DataTransferObject;

class UserData extends DataTransferObject
{
    public function __construct(
        public readonly ?string $email,
        public readonly ?string $username,
        public readonly ?string $password,
        public readonly ?string $mobileNumber,
        public readonly ?string $firstName,
        public readonly ?string $middleName,
        public readonly ?string $lastName,
        public readonly ?string $gender,
        public readonly ?string $dateOfBirth,
        public readonly ?string $address,
        public readonly ?string $city,
        public readonly ?string $state,
        public readonly ?string $country,
        public readonly ?string $passportPhoto,
        public readonly ?bool $requireCot,
        public readonly ?bool $requireImf,
        public readonly ?bool $requireTax,
    ) {
    }
}
