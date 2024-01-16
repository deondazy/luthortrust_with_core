<?php

declare(strict_types=1);

namespace Denosys\App\Database\Entities;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Denosys\App\Database\Entities\User;
use Doctrine\Common\Collections\Collection;
use Denosys\App\Repository\AccountRepository;
use Denosys\Core\Database\Factories\HasFactory;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\Common\Collections\ArrayCollection;
use Denosys\Core\Database\Entities\Traits\HasTimestamps;

#[ORM\Entity(repositoryClass: AccountRepository::class)]
#[ORM\Table(name: 'accounts')]
#[HasLifecycleCallbacks]
class Account
{
    use HasTimestamps;
    use HasFactory;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::BIGINT, length: 20)]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'users')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id')]
    private ?User $user = null;

    #[ORM\Column(type: Types::STRING)]
    private ?string $number = null;

    #[ORM\Column(type: Types::STRING)]
    private ?string $status = null;

    #[ORM\Column(type: Types::STRING)]
    private ?string $type = null;

    #[ORM\Column(type: Types::STRING)]
    private ?string $balance = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'created_by', referencedColumnName: 'id')]
    private User $createdBy;

    #[ORM\OneToMany(mappedBy: 'account', targetEntity: User::class)]
    private Collection $users;

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }
}
