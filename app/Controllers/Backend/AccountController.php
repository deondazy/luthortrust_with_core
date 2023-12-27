<?php

declare(strict_types=1);

namespace Denosys\App\Controllers\Backend;

use Denosys\Core\Controller\AbstractController;

class AccountController extends AbstractController
{
    public function index()
    {
        return $this->view('backend.accounts.index');
    }

}
