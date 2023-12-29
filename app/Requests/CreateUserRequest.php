<?php

declare(strict_types=1);

namespace Denosys\App\Requests;

use Denosys\App\DTO\UserDTO;
use Denosys\App\Services\UserCreationService;
use Denosys\Core\Http\FormRequest;

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
            'passportPhoto' => ['optional'],
            'requireCot'    => ['optional'],
            'requireImf'    => ['optional'],
            'requireTax'    => ['optional'],
        ];
    }

    public function createUser()
    {
        $this->validate();

        $userDto = UserDTO::createFromArray($this->validated());

        $this->userCreationService->createUser($userDto);
    }
}