<?php

declare(strict_types=1);

namespace Denosys\App\Controllers\Auth;

use Denosys\App\Requests\Auth\LoginRequest;
use Denosys\App\Services\User\UserAuthenticationService;
use Denosys\Core\Controller\AbstractController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface;

class AuthController extends AbstractController
{
    public function create(): Response
    {
        return $this->view('auth.login');
    }

    public function login(LoginRequest $loginRequest): Response
    {
        $loginRequest->authenticate();

        return redirectToRoute('account.index');
    }

    public function showRegistrationForm(): Response
    {
        return $this->view('auth.register');
    }

    public function register(
        ServerRequestInterface $request,
        UserAuthenticationService $authenticationService
    ): Response {
        $formData = $request->getParsedBody();

        $authenticationService->register($formData);

        return redirectToRoute('login');
    }

    public function forgotPassword(): Response
    {
        return $this->view('auth/forgot-password');
    }

    public function logout(UserAuthenticationService $authenticationService): Response
    {
        $authenticationService->logout();

        return redirectToRoute('home');
    }
}
