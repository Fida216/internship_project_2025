<?php

namespace App\Domain\Client\DTO;

use App\Domain\Client\Entity\Client;
use App\Shared\Enum\AcquisitionSource;
use App\Shared\Enum\Gender;
use App\Shared\Enum\Status;

class ClientResponseDTO
{
    public string $id;
    public string $lastName;
    public string $firstName;
    public string $birthDate;
    public string $email;
    public string $phone;
    public ?string $whatsapp;
    public ?string $nationalId;
    public ?string $passport;
    public string $nationality;
    public string $residence;
    public string $gender;
    public string $acquisitionSource;
    public string $status;
    public ?string $currentSegment;
    public string $createdAt;
    public array $exchangeOffice;

    public function __construct(Client $client)
    {
        $this->id = $client->getId()->toString();
        $this->lastName = $client->getLastName();
        $this->firstName = $client->getFirstName();
        $this->birthDate = $client->getBirthDate()->format('Y-m-d');
        $this->email = $client->getEmail();
        $this->phone = $client->getPhone();
        $this->whatsapp = $client->getWhatsapp();
        $this->nationalId = $client->getNationalId();
        $this->passport = $client->getPassport();
        $this->nationality = $client->getNationality();
        $this->residence = $client->getResidence();
        $this->gender = $client->getGender()->value;
        $this->acquisitionSource = $client->getAcquisitionSource()->value;
        $this->status = $client->getStatus()->value;
        $this->currentSegment = $client->getCurrentSegment();
        $this->createdAt = $client->getCreatedAt()->format('Y-m-d H:i:s');
        
        $this->exchangeOffice = [
            'id' => $client->getExchangeOffice()->getId()->toString(),
            'name' => $client->getExchangeOffice()->getName(),
            'address' => $client->getExchangeOffice()->getAddress()
        ];
    }


// Creates an instance from an array of data
    public static function fromArray(array $data): self
    {
        $dto = new self();
        foreach ($data as $property => $value) {
            if (property_exists($dto, $property)) {
                $dto->$property = $value;
            }
        }
        return $dto;
    }


// Converts the DTO to an array
    
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'lastName' => $this->lastName,
            'firstName' => $this->firstName,
            'birthDate' => $this->birthDate,
            'email' => $this->email,
            'phone' => $this->phone,
            'whatsapp' => $this->whatsapp,
            'nationalId' => $this->nationalId,
            'passport' => $this->passport,
            'nationality' => $this->nationality,
            'residence' => $this->residence,
            'gender' => $this->gender,
            'acquisitionSource' => $this->acquisitionSource,
            'status' => $this->status,
            'currentSegment' => $this->currentSegment,
            'createdAt' => $this->createdAt,
            'exchangeOffice' => $this->exchangeOffice
        ];
    }
}
