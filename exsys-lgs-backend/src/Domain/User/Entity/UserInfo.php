<?php


namespace App\Domain\User\Entity;

use App\Shared\Enum\Role;
use App\Shared\Enum\Status;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

use Doctrine\ORM\Mapping as ORM;
use App\Domain\User\Repository\UserInfoRepository;
use App\Domain\ExchangeOffice\Entity\ExchangeOffice;

#[ORM\Entity(repositoryClass: UserInfoRepository::class)]
#[ORM\Table(name: "userInfo")]
class UserInfo implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'Ramsey\\Uuid\\Doctrine\\UuidGenerator')]
    private UuidInterface $id;

    #[ORM\Column(type: "string")]
    private $lastName;

    #[ORM\Column(type: "string")]
    private $firstName;

    #[ORM\Column(type: "string")]
    private $phone;
    #[ORM\Column(type: "string", enumType: Role::class)]
    private Role $role;

    #[ORM\Column(type: "string", enumType: Status::class)]
    private Status $status;

    #[ORM\Column(type: "datetime")]
    private \DateTimeInterface $createdAt;

    #[ORM\OneToOne(targetEntity: UserCredential::class)]
    #[ORM\JoinColumn(name: "account_id", referencedColumnName: "id", unique: true)]
    private $account;

    #[ORM\ManyToOne(targetEntity: ExchangeOffice::class)]
    #[ORM\JoinColumn(name: "exchange_office_id", referencedColumnName: "id")]
    private $exchangeOffice;

    public function getId(): ?UuidInterface
    {
        return $this->id;
    }


    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;
        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;
        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;
        return $this;
    }

    public function getRole(): ?Role
    {
        return $this->role;
    }

    public function setRole(Role $role): self
    {
        $this->role = $role;
        return $this;
    }

    public function getStatus(): ?Status
    {
        return $this->status;
    }

    public function setStatus(Status $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getAccount(): ?UserCredential
    {
        return $this->account;
    }

    public function setAccount(?UserCredential $account): self
    {
        $this->account = $account;
        return $this;
    }

    public function getExchangeOffice(): ?ExchangeOffice
    {
        return $this->exchangeOffice;
    }

    public function setExchangeOffice(?ExchangeOffice $exchangeOffice): self
    {
        $this->exchangeOffice = $exchangeOffice;
        return $this;
    }


    public function getUserIdentifier(): string
    {
        return $this->account ? $this->account->getEmail() : '';
    }

    public function getRoles(): array
    {

        $roles = ['ROLE_USER'];
        if ($this->role) {
            $roles[] = $this->role->getSymfonyRole();
        }
        return array_unique($roles);
    }

    public function getPassword(): ?string
    {
        return $this->account ? $this->account->getPasswordHash() : null;
    }

    public function eraseCredentials(): void
    {
    }
    
}
