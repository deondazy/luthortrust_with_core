<?php

declare(strict_types=1);

namespace Denosys\App\Requests;

use Denosys\App\Database\Entities\Account;
use Denosys\App\Database\Entities\User;
use Denosys\App\Repository\AccountRepository;
use Denosys\Core\Http\FormRequest;
use Denosys\Core\Security\CurrentUser;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;

class CreateAccountRequest extends FormRequest
{
    public function __construct(
        private readonly AccountRepository $accountRepository,
        private readonly CurrentUser $currentUser,
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    public function rules(): array
    {
        return [
            'accountNumber' => ['required', 'min:10', 'max:10'],
            'accountType'   => ['required'],
            'accountStatus' => ['required'],
        ];
    }

    /**
     * @throws ORMException
     */
    public function createAccount(User $user): void
    {
        $validatedData = $this->validate();

        $account = new Account();
        $account->setNumber($validatedData['accountNumber']);
        $account->setType($validatedData['accountType']);
        $account->setStatus($validatedData['accountStatus']);
        $account->setUser($user);
        $account->setCreatedBy(
            $this->entityManager->getReference(User::class, $this->currentUser->getUser()->getId())
        );

        $this->accountRepository->save($account);
    }
}
