<?php

declare(strict_types=1);

namespace Denosys\App\Controllers\Admin;

use Denosys\App\Repository\UserRepository;
use Denosys\App\Repository\CountryRepository;
use Denosys\Core\Controller\AbstractController;
use Denosys\App\Services\UserAuthenticationService;
use Denosys\App\Services\UserUpdateService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ClientController extends AbstractController
{
    public function index(UserRepository $user, int $page = 1): Response
    {
        $latestClients = $user->findLatest($page);
        return $this->view('admin.client.index', compact('latestClients'));
    }

    public function create(CountryRepository $country): Response
    {
        $countries = $country->findAll();
        return $this->view('admin.client.create', compact('countries'));
    }

    public function store(Request $request, UserAuthenticationService $userService): Response
    {
        $userService->newUser($request);

        return $this->redirectToRoute('admin.client.index')
            ->withFlash('success', 'User created successfully');
    }

    public function edit(CountryRepository $country, UserRepository $user, int $id): Response
    {
        $client = $user->find($id);
        $countries = $country->findAll();

        return $this->view('admin.client.edit', compact('client', 'countries'));
    }

    public function update(Request $request, UserUpdateService $userUpdateService, int $id): Response
    {
        // if (is_null($client = $user->find($id))) {
        //     return $this->redirectToRoute('admin.client.index')
        //         ->withFlash('error', 'User not found');
        // }

        $userUpdateService->updateUser($id, $formData);

        return $this->redirectToRoute('admin.client.index')
            ->withFlash('success', 'User updated successfully');
    }

    public function delete(UserRepository $user, UserAuthenticationService $userService, int $id): Response
    {
        if (is_null($client = $user->find($id))) {
            return $this->redirectToRoute('admin.client.index')
                ->withFlash('error', 'User not found');
        }

        $userService->delete($client);

        return $this->redirectToRoute('admin.client.index')
            ->withFlash('success', 'User deleted successfully');
    }
}

