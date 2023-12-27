<?php

declare(strict_types=1);

namespace Denosys\App\Requests;

use Denosys\App\DTO\UserDTO;
use Denosys\App\Services\UserRegistrationService;
use Denosys\Core\Http\FormRequest;

class CreateUserRequest extends FormRequest
{
    public function __construct(private readonly UserRegistrationService $userAuthenticationService)
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

        $userDto = new UserDTO(
            email:         $this->validated()['email'],
            username:      $this->validated()['username'],
            password:      $this->validated()['password'],
            mobileNumber:  $this->validated()['mobileNumber'],
            firstName:     $this->validated()['firstName'],
            middleName:    ($this->validated()['middleName']) ?? null,
            lastName:      $this->validated()['lastName'],
            gender:        $this->validated()['gender'],
            dateOfBirth:   $this->validated()['dateOfBirth'],
            address:       $this->validated()['address'],
            city:          $this->validated()['city'],
            state:         $this->validated()['state'],
            country:       $this->validated()['country'],
            passportPhoto: null,
            requireCot: isset($validatedData['requireCot']) ? (bool) $validatedData['requireCot'] : null,
            requireImf: isset($validatedData['requireImf']) ? (bool) $validatedData['requireImf'] : null,
            requireTax: isset($validatedData['requireTax']) ? (bool) $validatedData['requireTax'] : null,
        );

        $this->userAuthenticationService->register($userDto);
    }
}