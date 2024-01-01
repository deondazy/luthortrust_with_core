<?php

declare(strict_types=1);

namespace Denosys\App\Database\Entities;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Denosys\App\Repository\CountryRepository;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity(repositoryClass: CountryRepository::class)]
#[ORM\Table(name: 'countries')]
class Country
{   
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::BIGINT, length: 20)]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING)]
    private ?string $name = null;

    #[ORM\Column(type: Types::STRING, length: 2, nullable: true)]
    private ?string $iso = null;

    #[ORM\Column(type: Types::STRING, length: 3, nullable: true)]
    private ?string $iso3 = null;

    #[ORM\Column(name: 'num_code', type: Types::INTEGER, nullable: true)]
    private ?int $numCode = null;

    #[ORM\Column(name: 'phone_code', type: Types::INTEGER, nullable: true)]
    private ?int $phoneCode = null;

    #[ORM\OneToMany(mappedBy: 'country', targetEntity: User::class)]
    private Collection $users;

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setName(string $name): Country
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setIso(string $iso): Country
    {
        $this->iso = $iso;

        return $this;
    }

    public function getIso(): ?string
    {
        return $this->iso;
    }

    public function setIso3(?string $iso3): Country
    {
        $this->iso3 = $iso3;

        return $this;
    }

    public function getIso3(): ?string
    {
        return $this->iso3;
    }

    public function setNumCode(?int $numCode): Country
    {
        $this->numCode = $numCode;

        return $this;
    }

    public function getNumCode(): ?int
    {
        return $this->numCode;
    }

    public function setPhoneCode(int $phoneCode): Country
    {
        $this->phoneCode = $phoneCode;

        return $this;
    }

    public function getPhoneCode(): ?int
    {
        return $this->phoneCode;
    }

    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): Country
    {
        $this->users->add($user);

        return $this;
    }
}
