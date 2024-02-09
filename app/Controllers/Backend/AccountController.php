<?php

declare(strict_types=1);

namespace Denosys\App\Controllers\Backend;

use Denosys\App\Database\Entities\User;
use Denosys\Core\Controller\AbstractController;
use Psr\Http\Message\ResponseInterface as Response;

class AccountController extends AbstractController
{
    public function index(User $user): Response
    {
        return $this->view('backend.accounts.index', [
            'user' => $user,
            'accounts' => $user->getAccounts(),
        ]);
    }

    public function create(User $user): Response
    {
        return $this->view('backend.accounts.create', [
            'user' => $user,
        ]);
    }
}
