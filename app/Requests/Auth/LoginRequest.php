<?php

declare(strict_types=1);

namespace Denosys\App\Requests\Auth;

use Denosys\Core\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'password' => ['required']
        ];
    }

    public function authenticate(): void
    {
        $this->validate();

        $this->authService->login($this->validated());

        
    }
}