<?php

declare(strict_types=1);

namespace Denosys\Core\Security;

use InvalidArgumentException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use Doctrine\Persistence\Mapping\ClassMetadata;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

class EntityUserProvider implements UserProviderInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly string $classOrAlias,
        private readonly ?string $property = null,
        private ?string $class = null
    ) {
    }

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        $repository = $this->getRepository();

        if (null !== $this->property) {
            $user = $repository->findOneBy([$this->property => $identifier]);
        } else {
            if (!$repository instanceof UserLoaderInterface) {
                throw new InvalidArgumentException(
                    sprintf(
                        'You must either make the "%s" entity Doctrine Repository ("%s") 
                        implement "Denosys\Core\Security\UserLoaderInterface" 
                        or set the "property" option in the corresponding entity provider configuration.',
                        $this->classOrAlias,
                        get_debug_type($repository)
                    )
                );
            }

            $user = $repository->loadUserByIdentifier($identifier);
        }

        if (null === $user) {
            $e = new UserNotFoundException(sprintf('User "%s" not found.', $identifier));
            $e->setUserIdentifier($identifier);

            throw $e;
        }

        return $user;
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        $class = $this->getClass();

        if (!$user instanceof $class) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_debug_type($user)));
        }

        $repository = $this->getRepository();

        if ($repository instanceof UserProviderInterface) {
            $refreshedUser = $repository->refreshUser($user);
        } else {
            // The user must be reloaded via the primary key as all other data
            // might have changed without proper persistence in the database.
            // That's the case when the user has been changed by a form with
            // validation errors.
            if (!$id = $this->getClassMetadata()->getIdentifierValues($user)) {
                throw new InvalidArgumentException(
                    'You cannot refresh a user from the EntityUserProvider that does not contain an identifier. 
                    The user object has to be serialized with its own identifier mapped by Doctrine.'
                );
            }

            $refreshedUser = $repository->find($id);

            if (null === $refreshedUser) {
                $e = new UserNotFoundException('User with id ' . json_encode($id) . ' not found.');
                $e->setUserIdentifier(json_encode($id));

                throw $e;
            }
        }

        return $refreshedUser;
    }

    public function supportsClass(string $class): bool
    {
        return $class === $this->getClass() || is_subclass_of($class, $this->getClass());
    }

    private function getRepository(): ObjectRepository
    {
        return $this->entityManager->getRepository($this->classOrAlias);
    }

    private function getClass(): string
    {
        if (!isset($this->class)) {
            $class = $this->classOrAlias;

            if (str_contains($class, ':')) {
                $class = $this->getClassMetadata()->getName();
            }

            $this->class = $class;
        }

        return $this->class;
    }

    private function getClassMetadata(): ClassMetadata
    {
        return $this->entityManager->getClassMetadata($this->classOrAlias);
    }
}
