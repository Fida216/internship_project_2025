<?php

namespace App\Domain\Client\Entity;

use App\Shared\Enum\AcquisitionSource;
use App\Shared\Enum\Gender;
use App\Shared\Enum\Status;
use App\Domain\Country\Entity\Country;
use Ramsey\Uuid\UuidInterface;
use Doctrine\ORM\Mapping as ORM;
use App\Domain\Client\Repository\ClientRepository;
use App\Domain\ExchangeOffice\Entity\ExchangeOffice;

#[ORM\Entity(repositoryClass: ClientRepository::class)]
#[ORM\Table(name: "client")]
class Client
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

    #[ORM\Column(type: "date")]
    private $birthDate;

    #[ORM\Column(type: "string")]
    private $email;

    #[ORM\Column(type: "string")]
    private $phone;

    #[ORM\Column(type: "string", nullable: true)]
    private $whatsapp;

    #[ORM\Column(type: "string", nullable: true)]
    private $nationalId;

    #[ORM\Column(type: "string", nullable: true)]
    private $passport;

    #[ORM\ManyToOne(targetEntity: Country::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Country $country;

    #[ORM\Column(type: "string")]
    private $residence;

    #[ORM\Column(type: "string", enumType: Gender::class)]
    private Gender $gender;

    #[ORM\Column(type: "string", enumType: AcquisitionSource::class)]
    private AcquisitionSource $acquisitionSource;

    #[ORM\Column(type: "string", enumType: Status::class)]
    private Status $status;

    #[ORM\Column(type: "string", nullable: true)]
    private $currentSegment;

    #[ORM\Column(type: "datetime")]
    private \DateTimeInterface $createdAt;

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

    public function getBirthDate(): ?\DateTimeInterface
    {
        return $this->birthDate;
    }

    public function setBirthDate(\DateTimeInterface $birthDate): self
    {
        $this->birthDate = $birthDate;
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

    public function getWhatsapp(): ?string
    {
        return $this->whatsapp;
    }

    public function setWhatsapp(?string $whatsapp): self
    {
        $this->whatsapp = $whatsapp;
        return $this;
    }

    public function getNationalId(): ?string
    {
        return $this->nationalId;
    }

    public function setNationalId(?string $nationalId): self
    {
        $this->nationalId = $nationalId;
        return $this;
    }

    public function getPassport(): ?string
    {
        return $this->passport;
    }

    public function setPassport(?string $passport): self
    {
        $this->passport = $passport;
        return $this;
    }

    public function getNationality(): ?string
    {
        return $this->country?->getNationality();
    }

    public function getCountry(): ?Country
    {
        return $this->country;
    }

    public function setCountry(Country $country): self
    {
        $this->country = $country;
        return $this;
    }

    public function getResidence(): ?string
    {
        return $this->residence;
    }

    public function setResidence(string $residence): self
    {
        $this->residence = $residence;
        return $this;
    }

    public function getGender(): ?Gender
    {
        return $this->gender;
    }

    public function setGender(Gender $gender): self
    {
        $this->gender = $gender;
        return $this;
    }

    public function getAcquisitionSource(): ?AcquisitionSource
    {
        return $this->acquisitionSource;
    }

    public function setAcquisitionSource(AcquisitionSource $acquisitionSource): self
    {
        $this->acquisitionSource = $acquisitionSource;
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

    public function getCurrentSegment(): ?string
    {
        return $this->currentSegment;
    }

    public function setCurrentSegment(?string $currentSegment): self
    {
        $this->currentSegment = $currentSegment;
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

    public function getExchangeOffice(): ?ExchangeOffice
    {
        return $this->exchangeOffice;
    }

    public function setExchangeOffice(?ExchangeOffice $exchangeOffice): self
    {
        $this->exchangeOffice = $exchangeOffice;
        return $this;
    }
}
