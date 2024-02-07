<?php

declare(strict_types=1);

namespace Denosys\App\Controllers\Backend;

use Denosys\App\Database\Entities\User;
use Denosys\App\Repository\UserRepository;
use Denosys\App\Repository\CountryRepository;
use Denosys\App\Requests\CreateUserRequest;
use Denosys\App\Requests\UpdateUserRequest;
use Denosys\App\Services\User\UserDeleteService;
use Denosys\Core\Controller\AbstractController;
use Exception;
use Psr\Http\Message\ResponseInterface as Response;

class UserController extends AbstractController
{
    public function index(UserRepository $user, int $page = 1): Response
    {
        return $this->view('backend.users.index', [
            'latestClients' => $user->findLatest($page)
        ]);
    }

    public function create(CountryRepository $country): Response
    {
        return $this->view('backend.users.create', [
            'countries' => $country->findAll()
        ]);
    }

    /**
     * @throws Exception
     */
    public function store(CreateUserRequest $userRequest): Response
    {
        $userRequest->createUser();

        return $this->redirectToRoute('backend.users.index')
            ->withFlash('success', 'User created successfully');
    }

    public function edit(CountryRepository $countryRepository, User $user): Response
    {
        return $this->view('backend.users.edit', [
            'user' => $user,
            'countries' => $countryRepository->findAll(),
        ]);
    }

    public function update(UpdateUserRequest $userRequest, User $user): Response
    {
        $userRequest->updateUser($user);

        return $this->redirectToRoute('backend.users.index')
            ->withFlash('success', 'User updated successfully');
    }

    public function delete(UserDeleteService $userService, User $user): Response
    {
        $userService->deleteUser($user);

        return $this->redirectToRoute('backend.users.index')
            ->withFlash('success', 'User deleted successfully');
    }
}

