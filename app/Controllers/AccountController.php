<?php

declare(strict_types=1);

namespace Denosys\App\Controllers;

use Denosys\Core\Controller\AbstractController;
use Denosys\Core\Security\CurrentUser;
use Psr\Http\Message\ResponseInterface;

class AccountController extends AbstractController
{
    public function index(CurrentUser $user): ResponseInterface
    {
        return $this->view('account.index', ['user' => $user->getUser()]);
    }

    public function profile(): ResponseInterface
    {
        return $this->view('account.profile');
    }
}
