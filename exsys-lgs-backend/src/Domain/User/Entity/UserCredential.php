<?php


namespace App\Domain\User\Entity;

use App\Shared\Enum\Status;
use Ramsey\Uuid\UuidInterface;

use Doctrine\ORM\Mapping as ORM;
use App\Domain\User\Repository\UserCredentialRepository;

#[ORM\Entity(repositoryClass: UserCredentialRepository::class)]
#[ORM\Table(name: "userCredential")]
class UserCredential
{

    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'Ramsey\\Uuid\\Doctrine\\UuidGenerator')]
    private UuidInterface $id;

    #[ORM\Column(type: "string", unique: true)]
    private $email;

    #[ORM\Column(type: "string")]
    private $passwordHash;
    #[ORM\Column(type: "string", enumType: Status::class)]
    private Status $status = Status::ACTIVE;

    #[ORM\Column(type: "datetime")]
    private \DateTimeInterface $createdAt;


    public function getId(): ?UuidInterface
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getPasswordHash(): ?string
    {
        return $this->passwordHash;
    }

    public function setPasswordHash(string $passwordHash): self
    {
        $this->passwordHash = $passwordHash;
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

    public function getStatus(): ?Status
    {
        return $this->status;
    }

    public function setStatus(Status $status): self
    {
        $this->status = $status;
        return $this;
    }
}
