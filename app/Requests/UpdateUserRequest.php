<?php

declare(strict_types=1);

namespace Denosys\App\Requests;

use Denosys\App\Database\Entities\User;
use Denosys\App\DTO\UserDTO;
use Denosys\Core\Http\FormRequest;
use Denosys\App\Services\UserUpdateService;

class UpdateUserRequest extends FormRequest
{
    public function __construct(private readonly UserUpdateService $userUpdateService)
    {
    }

    public function rules(): array
    {
        return [
            'username'      => ['required', 'alphaNum', 'min:3', 'max:50'],
            'email'         => ['required', 'email'],
            'mobileNumber'  => ['required'],
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

    public function updateUser(User $user): void
    {
        $this->validate();

        $userDto = UserDTO::createFromArray($this->validated());

        $this->userUpdateService->updateUser($user, $userDto);
    }
}