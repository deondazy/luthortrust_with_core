<?php

declare(strict_types=1);

namespace Denosys\App\Controllers\Backend;

use Denosys\Core\Controller\AbstractController;
use Psr\Http\Message\ResponseInterface as Response;

class DashboardController extends AbstractController
{
    public function index(): Response
    {
        return $this->view('backend.dashboard.index');
    }
}
