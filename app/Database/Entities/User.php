<?php

namespace Denosys\App\Database\Entities;

use DateTime;
use Denosys\App\Repository\UserRepository;
use Denosys\Core\Database\Factories\HasFactory;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Denosys\Core\Database\Entities\Traits\HasUuid;
use Symfony\Component\Security\Core\User\UserInterface;
use Denosys\Core\Database\Entities\Traits\HasTimestamps;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'users')]
#[HasLifecycleCallbacks]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    use HasTimestamps;
    use HasUuid;
    use HasFactory;

    public const USER_IDENTIFIER = 'username';

    final public const ROLE_USER = 'ROLE_USER';
    final public const ROLE_ADMIN = 'ROLE_ADMIN';
    final public const STATUS_PENDING = 'pending';
    final public const STATUS_ACTIVE = 'active';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::BIGINT, length: 20)]
    private ?int $id = null;

    #[ORM\Column(name: 'reference_id', type: Types::GUID, unique: true)]
    private ?string $referenceId = null;

    #[ORM\Column(name: 'first_name', type: Types::STRING)]
    private ?string $firstName = null;

    #[ORM\Column(name: 'middle_name', type: Types::STRING, nullable: true)]
    private ?string $middleName = null;

    #[ORM\Column(name: 'last_name', type: Types::STRING)]
    private ?string $lastName = null;

    #[ORM\Column(type: Types::STRING, unique: true)]
    private ?string $email = null;

    #[ORM\Column(type: Types::STRING, unique: true)]
    private ?string $username = null;

    #[ORM\Column(type: Types::STRING)]
    private ?string $gender = null;

    #[ORM\Column(name: 'date_of_birth', type: Types::DATE_MUTABLE)]
    private ?DateTime $dateOfBirth = null;

    #[ORM\Column(type: Types::STRING)]
    private ?string $address = null;

    #[ORM\Column(type: Types::STRING)]
    private ?string $city = null;

    #[ORM\Column(type: Types::STRING)]
    private ?string $state = null;

    #[ORM\ManyToOne(targetEntity: Country::class, inversedBy: 'users')]
    #[ORM\JoinColumn(name: 'country', referencedColumnName: 'id')]
    private ?Country $country = null;

    #[ORM\Column(name: 'mobile_number', type: Types::STRING)]
    private ?string $mobileNumber = null;

    #[ORM\Column(type: Types::STRING)]
    private ?string $password = null;

    #[ORM\Column(name: 'is_active', type: Types::BOOLEAN)]
    private bool $isActive = false;

    #[ORM\Column(type: Types::JSON)]
    private array $roles = [];

    #[ORM\Column(type: Types::STRING, length: 4)]
    private string $pin = '1234';

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $passport = null;

    #[ORM\Column(name: 'require_cot', type: Types::BOOLEAN)]
    private bool $requireCot = true;

    #[ORM\Column(name: 'require_imf', type: Types::BOOLEAN)]
    private bool $requireImf = false;

    #[ORM\Column(name: 'require_tax', type: Types::BOOLEAN)]
    private bool $requireTax = false;

    #[ORM\Column(name: 'cot_code', type: Types::STRING, nullable: true)]
    private ?string $cotCode = null;

    #[ORM\Column(name: 'imf_code', type: Types::STRING, nullable: true)]
    private ?string $imfCode = null;

    #[ORM\Column(name: 'tax_code', type: Types::STRING, nullable: true)]
    private ?string $taxCode = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'created_by', referencedColumnName: 'id')]
    private ?User $createdBy = null;

    #[ORM\Column(type: Types::STRING)]
    private ?string $status = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Session::class)]
    private Collection $sessions;

    public function __construct()
    {
        $this->sessions = new ArrayCollection();
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->{self::USER_IDENTIFIER};
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setUuid(string $uuid): User
    {
        $this->referenceId = $uuid;

        return $this;
    }

    public function getUuid(): ?string
    {
        return $this->referenceId;
    }

    public function setFirstName(string $firstName): User
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setMiddleName(?string $middleName): User
    {
        $this->middleName = $middleName;

        return $this;
    }

    public function getMiddleName(): ?string
    {
        return $this->middleName;
    }

    public function setLastName(string $lastName): User
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setEmail(string $email): User
    {
        $this->email = $email;

        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setUsername(string $username): User
    {
        $this->username = $username;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setPassword(string $password): User
    {
        $this->password = $password;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setRoles(array $roles): User
    {
        $this->roles = $roles;

        return $this;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;

        if (empty($roles)) {
            $roles[] = self::ROLE_USER;
        }

        return array_unique($roles);
    }

    public function getSessions(): Collection
    {
        return $this->sessions;
    }

    public function addSession(Session $session): User
    {
        $this->sessions->add($session);

        return $this;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender($gender): User
    {
        $this->gender = $gender;

        return $this;
    }

    public function getDateOfBirth(): ?DateTime
    {
        return $this->dateOfBirth;
    }

    public function setDateOfBirth(DateTime $dateOfBirth): User
    {
        $this->dateOfBirth = $dateOfBirth;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress($address): User
    {
        $this->address = $address;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity($city): User
    {
        $this->city = $city;

        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState($state): User
    {
        $this->state = $state;

        return $this;
    }

    public function getCountry(): ?Country
    {
        return $this->country;
    }

    public function setCountry(?Country $country): User
    {
        $country->addUser($this);

        $this->country = $country;

        return $this;
    }

    public function getMobileNumber(): ?string
    {
        return $this->mobileNumber;
    }

    public function setMobileNumber(?string $mobileNumber): User
    {
        $this->mobileNumber = $mobileNumber;

        return $this;
    }

    public function getIsActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): User
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getPin(): string
    {
        return $this->pin;
    }

    public function setPin(string $pin): User
    {
        $this->pin = $pin;

        return $this;
    }

    public function getPassport(): ?string
    {
        return $this->passport;
    }

    public function setPassport(?string $passport): User
    {
        $this->passport = $passport;

        return $this;
    }

    public function getRequireCot(): bool
    {
        return $this->requireCot;
    }

    public function setRequireCot(bool $requireCot): User
    {
        $this->requireCot = $requireCot;

        return $this;
    }

    public function getRequireImf(): bool
    {
        return $this->requireImf;
    }

    public function setRequireImf(bool $requireImf): User
    {
        $this->requireImf = $requireImf;

        return $this;
    }

    public function getRequireTax(): bool
    {
        return $this->requireTax;
    }

    public function setRequireTax(bool $requireTax): User
    {
        $this->requireTax = $requireTax;

        return $this;
    }

    public function getCotCode(): ?string
    {
        return $this->cotCode;
    }

    public function setCotCode(?string $cotCode): User
    {
        $this->cotCode = $cotCode;

        return $this;
    }

    public function getImfCode(): ?string
    {
        return $this->imfCode;
    }

    public function setImfCode(?string $imfCode): User
    {
        $this->imfCode = $imfCode;

        return $this;
    }

    public function getTaxCode(): ?string
    {
        return $this->taxCode;
    }

    public function setTaxCode(?string $taxCode): User
    {
        $this->taxCode = $taxCode;

        return $this;
    }

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?User $createdBy): User
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): User
    {
        $this->status = $status;

        return $this;
    }

    public function eraseCredentials(): void
    {
        $this->password = null;
    }

    public function __serialize(): array
    {
        return [$this->id, $this->referenceId, $this->username];
    }

    public function __unserialize(array $data): void
    {
        [$this->id, $this->referenceId, $this->username] = $data;
    }
}
