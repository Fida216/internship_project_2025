<?php

namespace App\Domain\Client\Mapper;

use App\Domain\Client\DTO\CreateClientDTO;
use App\Domain\Client\DTO\UpdateClientDTO;
use App\Domain\Client\DTO\ClientResponseDTO;
use App\Domain\Client\Entity\Client;
use App\Domain\ExchangeOffice\Entity\ExchangeOffice;
use App\Domain\ExchangeOffice\Repository\ExchangeOfficeRepository;
use App\Domain\Country\Service\CountryService;
use App\Shared\Enum\Status;
use DateTime;

class ClientMapper
{
    public function __construct(
        private ExchangeOfficeRepository $exchangeOfficeRepository,
        private CountryService $countryService
    ) {}


    public function createDtoToEntity(CreateClientDTO $dto, string $exchangeOfficeId): Client
    {
        $client = new Client();
        
        $client->setLastName($dto->lastName);
        $client->setFirstName($dto->firstName);
        $client->setBirthDate(new DateTime($dto->birthDate));
        $client->setEmail($dto->email);
        $client->setPhone($dto->phone);
        $client->setWhatsapp($dto->whatsapp);
        $client->setNationalId($dto->nationalId);
        $client->setPassport($dto->passport);
        
        $country = $this->countryService->findByNationality($dto->nationality);
        if (!$country) {
            throw new \InvalidArgumentException('Invalid nationality: ' . $dto->nationality);
        }
        $client->setCountry($country);
        
        $client->setResidence($dto->residence);
        $client->setGender($dto->gender);
        $client->setAcquisitionSource($dto->acquisitionSource);
        $client->setCurrentSegment($dto->currentSegment);
                $client->setStatus(Status::ACTIVE); 
        $client->setCreatedAt(new DateTime());

        $exchangeOffice = $this->exchangeOfficeRepository->find($exchangeOfficeId);
        if (!$exchangeOffice) {
            throw new \InvalidArgumentException('Exchange office not found');
        }
        $client->setExchangeOffice($exchangeOffice);
        
        return $client;
    }



    public function updateEntityFromDto(Client $client, UpdateClientDTO $dto): Client
    {
        if ($dto->lastName !== null) {
            $client->setLastName($dto->lastName);
        }
        
        if ($dto->firstName !== null) {
            $client->setFirstName($dto->firstName);
        }
        
        if ($dto->birthDate !== null) {
            $client->setBirthDate(new DateTime($dto->birthDate));
        }
        
        if ($dto->email !== null) {
            $client->setEmail($dto->email);
        }
        
        if ($dto->phone !== null) {
            $client->setPhone($dto->phone);
        }
        
        if ($dto->whatsapp !== null) {
            $client->setWhatsapp($dto->whatsapp);
        }
        
        if ($dto->nationalId !== null) {
            $client->setNationalId($dto->nationalId);
        }
        
        if ($dto->passport !== null) {
            $client->setPassport($dto->passport);
        }
        
        if ($dto->nationality !== null) {
            $country = $this->countryService->findByNationality($dto->nationality);
            if (!$country) {
                throw new \InvalidArgumentException('Invalid nationality: ' . $dto->nationality);
            }
            $client->setCountry($country);
        }
        
        if ($dto->residence !== null) {
            $client->setResidence($dto->residence);
        }
        
        if ($dto->gender !== null) {
            $client->setGender($dto->gender);
        }
        
        if ($dto->acquisitionSource !== null) {
            $client->setAcquisitionSource($dto->acquisitionSource);
        }
        
        if ($dto->status !== null) {
            $client->setStatus($dto->status);
        }
        
        if ($dto->currentSegment !== null) {
            $client->setCurrentSegment($dto->currentSegment);
        }
        
        return $client;
    }


     
    public function entityToResponseDto(Client $client): ClientResponseDTO
    {
        return new ClientResponseDTO($client);
    }


    public function entitiesToResponseDtos(array $clients): array
    {
        return array_map(fn(Client $client) => $this->entityToResponseDto($client), $clients);
    }
}
