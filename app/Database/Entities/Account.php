<?php

declare(strict_types=1);

namespace Denosys\App\Database\Entities;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Denosys\App\Repository\AccountRepository;
use Denosys\Core\Database\Factories\HasFactory;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
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

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'accounts')]
    private ?User $user = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'created_by', referencedColumnName: 'id')]
    private User $createdBy;

    #[ORM\Column(type: Types::STRING)]
    private ?string $number = null;

    #[ORM\Column(type: Types::STRING)]
    private ?string $status = null;

    #[ORM\Column(type: Types::STRING)]
    private ?string $type = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setUser(User $user): Account
    {
        $user->addAccount($this);

        $this->user = $user;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setNumber(string $number): Account
    {
        $this->number = $number;

        return $this;
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setStatus(string $status): Account
    {
        $this->status = $status;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setType(string $type): Account
    {
        $this->type = $type;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setCreatedBy(User $createdBy): Account
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }
}
