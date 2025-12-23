<?php


namespace App\Domain\ExchangeOffice\Entity;

use Ramsey\Uuid\UuidInterface;
use App\Shared\Enum\Status;
use Doctrine\ORM\Mapping as ORM;
use App\Domain\ExchangeOffice\Repository\ExchangeOfficeRepository;

#[ORM\Entity(repositoryClass: ExchangeOfficeRepository::class)]
#[ORM\Table(name: "exchangeOffice")]
class ExchangeOffice
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'Ramsey\\Uuid\\Doctrine\\UuidGenerator')]
    private UuidInterface $id;

    #[ORM\Column(type: "string")]
    private $name;

    #[ORM\Column(type: "string")]
    private $address;

    #[ORM\Column(type: "string")]
    private $email;

    #[ORM\Column(type: "string")]
    private $phone;

    #[ORM\Column(type: "string")]
    private $owner;

    #[ORM\Column(type: "string", enumType: Status::class)]
    private Status $officeStatus;

    #[ORM\Column(type: "datetime")]
    private \DateTimeInterface $createdAt;

    public function getId(): ?UuidInterface
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;
        return $this;
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

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;
        return $this;
    }

    public function getOwner(): ?string
    {
        return $this->owner;
    }

    public function setOwner(string $owner): self
    {
        $this->owner = $owner;
        return $this;
    }

    public function getOfficeStatus(): ?Status
    {
        return $this->officeStatus;
    }

    public function setOfficeStatus(Status $officeStatus): self
    {
        $this->officeStatus = $officeStatus;
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
}
