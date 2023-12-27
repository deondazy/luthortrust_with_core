<?php

declare(strict_types=1);

namespace Denosys\App\Controllers\Backend;

use Denosys\App\Database\Entities\User;
use Denosys\App\Repository\UserRepository;
use Denosys\App\Repository\CountryRepository;
use Denosys\App\Requests\CreateUserRequest;
use Denosys\App\Requests\UpdateUserRequest;
use Denosys\Core\Controller\AbstractController;
use Psr\Http\Message\ResponseInterface as Response;

class ClientController extends AbstractController
{
    public function index(UserRepository $user, int $page = 1): Response
    {
        return $this->view('backend.client.index', [
            'latestClients' => $user->findLatest($page)
        ]);
    }

    public function create(CountryRepository $country): Response
    {
        return $this->view('backend.client.create', [
            'countries' => $country->findAll()
        ]);
    }

    public function store(CreateUserRequest $userRequest): Response
    {
        $userRequest->createUser();

        return $this->redirectToRoute('backend.client.index')
            ->withFlash('success', 'User created successfully');
    }

    public function edit(CountryRepository $countryRepository, User $user): Response
    {
        return $this->view('backend.client.edit', [
            'client' => $user,
            'countries' => $countryRepository->findAll(),
        ]);
    }

    public function update(UpdateUserRequest $userRequest, User $user): Response
    {
        $userRequest->updateUser($user);

        return $this->redirectToRoute('backend.client.index')
            ->withFlash('success', 'User updated successfully');
    }

    public function delete(UserRepository $userRepository, User $user): Response
    {
        $userRepository->delete($user);

        return $this->redirectToRoute('backend.client.index')
            ->withFlash('success', 'User deleted successfully');
    }
}

