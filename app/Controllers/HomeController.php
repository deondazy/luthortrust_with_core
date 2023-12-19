<?php

declare(strict_types=1);

namespace Denosys\App\Controllers;

use Denosys\Core\Controller\AbstractController;
use Psr\Http\Message\ResponseInterface;

class HomeController extends AbstractController
{
    public function index(): ResponseInterface
    {
        return $this->view('home.index');
    }

    public function loans(): ResponseInterface
    {
        return $this->view('home.loans');
    }

    public function mortgage(): ResponseInterface
    {
        return $this->view('home.mortgage');
    }

    public function investments(): ResponseInterface
    {
        return $this->view('home.investments');
    }

    public function digitalServices(): ResponseInterface
    {
        return $this->view('home.digital-services');
    }

    public function showContactForm(): ResponseInterface
    {
        return $this->view('home.contact');
    }

    public function contact(): ResponseInterface
    {
        $this->flash('success', 'Thank you for contacting us. We will get back to you shortly.');
        return $this->redirect('/contact');
    }

    public function privacy(): ResponseInterface
    {
        return $this->view('home.privacy');
    }
}
