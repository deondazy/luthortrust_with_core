<?php

declare(strict_types=1);

namespace Denosys\App\Requests;

use Denosys\App\DTO\UserDTO;
use Denosys\App\Services\UserCreationService;
use Denosys\Core\Http\FormRequest;
use Denosys\Core\Validation\ValidationException;
use Psr\Http\Message\UploadedFileInterface;

class CreateUserRequest extends FormRequest
{
    public function __construct(private readonly UserCreationService $userCreationService)
    {
    }

    public function rules(): array
    {
        return [
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
            'requireCot'    => ['optional'],
            'requireImf'    => ['optional'],
            'requireTax'    => ['optional'],
        ];
    }

    public function createUser()
    {
        $validatedData = $this->validate();

        if ($this->hasFile('passportPhoto')) {
            $validatedData['passportPhoto'] = $this->getPassportPhoto($this->file('passportPhoto'));
        }

        $userDto = UserDTO::createFromArray($validatedData);

        $this->userCreationService->createUser($userDto);
    }

    private function getPassportPhoto(UploadedFileInterface $file): ?UploadedFileInterface
    {
        $this->validate(['passportPhoto' => ['optional']]);
            
        if ($file->getError() === UPLOAD_ERR_OK) {
            return $file;
        }

        return null;
    }
}