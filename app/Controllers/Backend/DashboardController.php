<?php

declare(strict_types=1);

namespace Denosys\App\Controllers\Backend;

use Denosys\Core\Controller\AbstractController;
use Denosys\Core\Security\CurrentUser;
use Psr\Http\Message\ResponseInterface as Response;

class DashboardController extends AbstractController
{
    public function index(CurrentUser $user): Response
    {
        return $this->view('backend.dashboard.index', ['user' => $user->getUser()]);
    }
}
