<?php

declare(strict_types=1);

use Denosys\App\Database\Entities\User;
use Denosys\App\Services\UserAuthenticationService;
use Denosys\Core\Config\ConfigurationInterface;
use Denosys\Core\Encryption\Encrypter;
use Denosys\Core\Encryption\EncrypterInterface;
use Denosys\Core\Security\CurrentUser;
use Denosys\Core\Security\EncryptedSessionTokenStorage;
use Denosys\Core\Security\EntityUserProvider;
use Denosys\Core\Session\SessionInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\AbstractManagerRegistry;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Slim\Psr7\Factory\ResponseFactory;
use Symfony\Component\PasswordHasher\Hasher\NativePasswordHasher;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Authentication\AuthenticationTrustResolver;
use Symfony\Component\Security\Core\Authentication\AuthenticationTrustResolverInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManager;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
use Symfony\Component\Security\Core\Authorization\Voter\RoleHierarchyVoter;
use Symfony\Component\Security\Core\Authorization\Voter\RoleVoter;
use Symfony\Component\Security\Core\Role\RoleHierarchy;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Valitron\Validator;

return [
    ResponseFactoryInterface::class => fn () => new ResponseFactory(),

    UserPasswordHasherInterface::class => fn (ContainerInterface $container)
        => $container->get(UserPasswordHasher::class),

    UserPasswordHasher::class => fn ()
        => new UserPasswordHasher(new PasswordHasherFactory(
            [
                User::class => new NativePasswordHasher(),
            ]
        )),

    UserAuthenticationService::class => fn (ContainerInterface $container)
        => new UserAuthenticationService(
            $container->get(EntityManagerInterface::class),
            $container->get(UserPasswordHasherInterface::class),
            $container->get(TokenStorageInterface::class),
            $container->get(AuthenticationTrustResolverInterface::class),
            $container->get(CurrentUser::class)
        ),

    TokenStorageInterface::class => fn (ContainerInterface $container)
        => new EncryptedSessionTokenStorage(
            $container->get(EncrypterInterface::class),
            $container->get(SessionInterface::class),
            $container->get('config')->get('session.name')
        ),

    Validator::class => fn () => new Validator(),

    EncrypterInterface::class => fn (ConfigurationInterface $config)
        => new Encrypter($config->get('app.key')),

    AuthenticationTrustResolverInterface::class => fn ()
        => new AuthenticationTrustResolver(),

    AuthorizationCheckerInterface::class => fn (ContainerInterface $container)
        => new AuthorizationChecker(
            $container->get(TokenStorageInterface::class),
            new AccessDecisionManager([
                new AuthenticatedVoter(new AuthenticationTrustResolver()),
                new RoleVoter(),
                new RoleHierarchyVoter(new RoleHierarchy([
                    User::ROLE_ADMIN => [User::ROLE_USER],
                ]))
            ])
        ),

    CurrentUser::class => fn (ContainerInterface $container)
        => new CurrentUser(
            $container->get(TokenStorageInterface::class),
            $container->get(UserProviderInterface::class)
        ),

    UserProviderInterface::class => fn (ContainerInterface $container) => new EntityUserProvider(
        $container->get(EntityManagerInterface::class),
        User::class,
        User::USER_IDENTIFIER
    ),
];
