<?php

use Denosys\App\Controllers\AuthController;
use Denosys\App\Controllers\HomeController;
use Denosys\App\Controllers\AccountController;
use Denosys\App\Database\Entities\Country;
use Denosys\App\Database\Entities\User;
use Denosys\App\Middleware\AdminAccessMiddleware;
use Denosys\App\Middleware\GuestMiddleware;
use Denosys\App\Middleware\AuthMiddleware;
use Denosys\App\Controllers\Admin\ClientController;
use Denosys\App\Controllers\Admin\DashboardController;
use Denosys\Core\Routing\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

return function ($router) {
    $router->get('/', [HomeController::class, 'index'])->setName('home');
    $router->get('/loans', [HomeController::class, 'loans'])->setName('loans');
    $router->get('/mortgage', [HomeController::class, 'mortgage'])->setName('mortgage');
    $router->get('/investments', [HomeController::class, 'investments'])->setName('investments');
    $router->get('/digital-services', [HomeController::class, 'digitalServices'])->setName('digital-services');
    $router->get('/contact', [HomeController::class, 'showContactForm'])->setName('contact');
    $router->post('/contact', [HomeController::class, 'contact'])->setName('contact.post');
    $router->get('/privacy', [HomeController::class, 'privacy'])->setName('privacy');

    // Authentication Routes
    $router->group('', function ($router) {
        $router->get('/login', [AuthController::class, 'showLoginForm'])->setName('login');
        $router->post('/login', [AuthController::class, 'login'])->setName('login.post');
        $router->get('/register', [AuthController::class, 'showRegistrationForm'])->setName('register');
        $router->post('/register', [AuthController::class, 'register'])->setName('register.post');
        $router->get('/forgot-password', [AuthController::class, 'forgotPassword'])->setName('forgot-password');
    })->add(GuestMiddleware::class);

    $router->post('/logout', [AuthController::class, 'logout'])->setName('logout');


    $router->group('/account', function ($router) {
        $router->get('', [AccountController::class, 'index'])->setName('account.index');
        $router->get('/profile', [AccountController::class, 'profile'])->setName('account.profile');
    })->add(AuthMiddleware::class);

    // Admin Routes
    $router->group('/admin', function ($router) {
        $router->get('', [DashboardController::class, 'index'])->setName('admin.index');

        // Client Routes
        $router->get('/clients[/{page}]', [ClientController::class, 'index'])->setName('admin.client.index');
        $router->get('/client/new', [ClientController::class, 'create'])->setName('admin.client.create');
        $router->post('/client/new', [ClientController::class, 'store'])->setName('admin.client.store');
        $router->get('/client/edit/{id}', [ClientController::class, 'edit'])->setName('admin.client.edit');
        $router->post('/client/edit/{id}', [ClientController::class, 'update'])->setName('admin.client.update');
        $router->post('/client/delete/{id}', [ClientController::class, 'delete'])->setName('admin.client.delete');
    })->add(AuthMiddleware::class)->add(AdminAccessMiddleware::class);

    $router->get('/add-user', function (
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ) {
        $user = new User();
        $user->setFirstName('William')
            ->setLastName('Smith')
            ->setEmail('willismith@zoho.com')
            ->setUsername('willismith')
            ->setGender('male')
            ->setDateOfBirth(new DateTime('1965-02-08'))
            ->setAddress('123 Main Street, New York, NY 10001')
            ->setCity('New York')
            ->setState('NY')
            ->setCountry($entityManager->getReference(Country::class, 226)) // USA
            ->setMobileNumber('212-555-1212')
            ->setPassword($passwordHasher->hashPassword($user, 'password'))
            ->setIsActive(true)
            ->setRoles(['ROLE_ADMIN'])
            ->setPin('0424')
            ->setStatus('active');

        $entityManager->persist($user);
        $entityManager->flush();

        return 'User added successfully';
    });

    //$router->get('/test', function (\Psr\Http\Message\ResponseInterface $response): \Psr\Http\Message\ResponseInterface {
    //    $messages = null;
    //
    //    dd(empty($messages));
    //
    //
    //    $response->getBody()->write('Hello World');
    //    return $response;
    //});
};
