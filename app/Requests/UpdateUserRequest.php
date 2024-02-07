<?php

declare(strict_types=1);

namespace Denosys\App\Requests;

use Denosys\App\Database\Entities\User;
use Denosys\App\DTO\UserDTO;
use Denosys\App\Services\User\UserUpdateService;
use Denosys\Core\Http\FormRequest;
use Exception;
use Psr\Http\Message\UploadedFileInterface;

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

    /**
     * @throws Exception
     */
    public function updateUser(User $user): void
    {
        $validatedData = $this->validate();

        if ($this->hasFile('passportPhoto')) {
            $validatedData['passportPhoto'] = $this->validatePassportPhoto($this->file('passportPhoto'));
        }

        $userDto = UserDTO::createFromArray($validatedData);

        $this->userUpdateService->updateUser($user, $userDto);
    }

    private function validatePassportPhoto(UploadedFileInterface $file): ?UploadedFileInterface
    {
        $this->validate(['passportPhoto' => ['optional']]);

        if ($file->getError() === UPLOAD_ERR_OK) {
            return $file;
        }

        return null;
    }
}
