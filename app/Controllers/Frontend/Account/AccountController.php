<?php

declare(strict_types=1);

namespace Denosys\App\Controllers\Frontend\Account;

use Denosys\Core\Controller\AbstractController;
use Psr\Http\Message\ResponseInterface;

class AccountController extends AbstractController
{
    public function index(): ResponseInterface
    {
        return $this->view('account.index');
    }

    public function profile(): ResponseInterface
    {
        return $this->view('account.profile');
    }
}
