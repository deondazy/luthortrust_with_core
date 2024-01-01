<?php

declare(strict_types=1);

namespace Denosys\App\Controllers\Frontend;

use Denosys\Core\Controller\AbstractController;
use Psr\Http\Message\ResponseInterface as Response;

class HomeController extends AbstractController
{
    public function index(): Response
    {
        return $this->view('home.index');
    }

    public function loans(): Response
    {
        return $this->view('home.loans');
    }

    public function mortgage(): Response
    {
        return $this->view('home.mortgage');
    }

    public function investments(): Response
    {
        return $this->view('home.investments');
    }

    public function digitalServices(): Response
    {
        return $this->view('home.digital-services');
    }

    public function showContactForm(): Response
    {
        return $this->view('home.contact');
    }

    public function contact(): Response
    {
        $this->flash('success', 'Thank you for contacting us. We will get back to you shortly.');
        return $this->redirect('/contact');
    }

    public function privacy(): Response
    {
        return $this->view('home.privacy');
    }
}
