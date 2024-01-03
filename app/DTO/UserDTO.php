<?php

declare(strict_types=1);

namespace Denosys\App\DTO;

use Denosys\Core\Support\DataTransferObject;
use Psr\Http\Message\UploadedFileInterface;

class UserDTO extends DataTransferObject
{
    public function __construct(
        public readonly string $email,
        public readonly string $username,
        public readonly ?string $password,
        public readonly ?string $mobileNumber,
        public readonly string $firstName,
        public readonly ?string $middleName,
        public readonly string $lastName,
        public readonly ?string $gender,
        public readonly string $dateOfBirth,
        public readonly ?string $address,
        public readonly ?string $city,
        public readonly ?string $state,
        public readonly ?string $country,
        public readonly ?UploadedFileInterface $passportPhoto,
        public readonly bool $requireCot = false,
        public readonly bool $requireImf = false,
        public readonly bool $requireTax = false,
    ) {
    }
}
