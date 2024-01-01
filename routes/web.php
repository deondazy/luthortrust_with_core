<?php

use Denosys\App\Database\Entities\User;
use Doctrine\ORM\EntityManagerInterface;
use Denosys\App\Database\Entities\Country;
use Denosys\App\Middleware\AuthMiddleware;
use Denosys\App\Middleware\GuestMiddleware;
use Denosys\App\Controllers\Auth\AuthController;
use Denosys\App\Middleware\AdminAccessMiddleware;
use Slim\Interfaces\RouteCollectorProxyInterface;
use Denosys\App\Controllers\Frontend\HomeController;
use Denosys\App\Controllers\Backend\ClientController;
use Denosys\App\Controllers\Backend\DashboardController;
use Denosys\App\Controllers\Frontend\Account\AccountController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Denosys\App\Controllers\Backend\AccountController as BackendAccountController;

return function (RouteCollectorProxyInterface $router) {
    $router->get('/', [HomeController::class, 'index'])->setName('home');
    $router->get('/loans', [HomeController::class, 'loans'])->setName('loans');
    $router->get('/mortgage', [HomeController::class, 'mortgage'])->setName('mortgage');
    $router->get('/investments', [HomeController::class, 'investments'])->setName('investments');
    $router->get('/digital-services', [HomeController::class, 'digitalServices'])->setName('digital-services');
    $router->get('/contact', [HomeController::class, 'showContactForm'])->setName('contact');
    $router->post('/contact', [HomeController::class, 'contact'])->setName('contact.post');
    $router->get('/privacy', [HomeController::class, 'privacy'])->setName('privacy');

    // Authentication Routes
    $router->group('', function (RouteCollectorProxyInterface $router) {
        $router->get('/login', [AuthController::class, 'create'])->setName('login');
        $router->post('/login', [AuthController::class, 'login'])->setName('login.post');
        $router->get('/register', [AuthController::class, 'showRegistrationForm'])->setName('register');
        $router->post('/register', [AuthController::class, 'register'])->setName('register.post');
        $router->get('/forgot-password', [AuthController::class, 'forgotPassword'])->setName('forgot-password');
    })->add(GuestMiddleware::class);

    $router->post('/logout', [AuthController::class, 'logout'])->setName('logout');

    $router->group('/account', function (RouteCollectorProxyInterface $router) {
        $router->get('', [AccountController::class, 'index'])->setName('account.index');
        $router->get('/profile', [AccountController::class, 'profile'])->setName('account.profile');
    })->add(AuthMiddleware::class);

    // Admin Routes
    $router->group('/admin', function (RouteCollectorProxyInterface $router) {
        $router->get('', [DashboardController::class, 'index'])->setName('backend.index');

        // Client Routes
        $router->get('/clients[/{page}]', [ClientController::class, 'index'])->setName('backend.client.index');
        $router->get('/client/new', [ClientController::class, 'create'])->setName('backend.client.create');
        $router->post('/client/new', [ClientController::class, 'store'])->setName('backend.client.store');
        $router->get('/client/edit/{user}', [ClientController::class, 'edit'])->setName('backend.client.edit');
        $router->post('/client/edit/{user}', [ClientController::class, 'update'])->setName('backend.client.update');
        $router->post('/client/delete/{user}', [ClientController::class, 'delete'])->setName('backend.client.delete');

        // Accounts Routes
        $router->get('/accounts[/{page}]', [BackendAccountController::class, 'index'])->setName('backend.accounts.index');
        $router->get('/account/new/{userId}', [BackendAccountController::class, 'create'])->setName('backend.accounts.create');
        $router->post('/account/new/{userId}', [BackendAccountController::class,'store'])->setName('backend.accounts.store');
        $router->get('/account/edit/{id}', [BackendAccountController::class, 'edit'])->setName('backend.accounts.edit');
        $router->post('/account/edit/{id}', [BackendAccountController::class, 'update'])->setName('backend.accounts.update');
        $router->post('/account/delete/{id}', [BackendAccountController::class, 'delete'])->setName('backend.accounts.delete');
    })->add(AdminAccessMiddleware::class);

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
};
