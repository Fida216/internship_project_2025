<?php

namespace App\Domain\ExchangeOffice\Mapper;

use App\Domain\ExchangeOffice\DTO\CreateExchangeOfficeDTO;
use App\Domain\ExchangeOffice\DTO\UpdateExchangeOfficeDTO;
use App\Domain\ExchangeOffice\DTO\ExchangeOfficeResponseDTO;
use App\Domain\ExchangeOffice\Entity\ExchangeOffice;
use App\Domain\User\Entity\UserInfo;
use App\Shared\Enum\Status;

class ExchangeOfficeMapper
{

    public function toEntityFromCreateDto(CreateExchangeOfficeDTO $dto): ExchangeOffice
    {
        $exchangeOffice = new ExchangeOffice();
        $exchangeOffice->setName($dto->name);
        $exchangeOffice->setAddress($dto->address);
        $exchangeOffice->setEmail($dto->email);
        $exchangeOffice->setPhone($dto->phone);
        $exchangeOffice->setOwner($dto->owner);
        $exchangeOffice->setOfficeStatus(Status::ACTIVE);
        $exchangeOffice->setCreatedAt(new \DateTime());

        return $exchangeOffice;
    }


    public function updateEntityFromDto(ExchangeOffice $exchangeOffice, UpdateExchangeOfficeDTO $dto): ExchangeOffice
    {
        if (isset($dto->name)) {
            $exchangeOffice->setName($dto->name);
        }
        if (isset($dto->address)) {
            $exchangeOffice->setAddress($dto->address);
        }
        if (isset($dto->email)) {
            $exchangeOffice->setEmail($dto->email);
        }
        if (isset($dto->phone)) {
            $exchangeOffice->setPhone($dto->phone);
        }
        if (isset($dto->owner)) {
            $exchangeOffice->setOwner($dto->owner);
        }
        if (isset($dto->status)) {
            $exchangeOffice->setOfficeStatus($dto->status);
        }

        return $exchangeOffice;
    }

 
    public function toResponseDto(ExchangeOffice $exchangeOffice): ExchangeOfficeResponseDTO
    {
        $data = [
            'id' => $exchangeOffice->getId(),
            'name' => $exchangeOffice->getName(),
            'address' => $exchangeOffice->getAddress(),
            'email' => $exchangeOffice->getEmail(),
            'phone' => $exchangeOffice->getPhone(),
            'owner' => $exchangeOffice->getOwner(),
            'status' => $exchangeOffice->getOfficeStatus()->value,
            'createdAt' => $exchangeOffice->getCreatedAt()?->format('Y-m-d H:i:s')
        ];

        return new ExchangeOfficeResponseDTO($data);
    }

    public function toResponseDtoArray(array $exchangeOffices): array
    {
        return array_map(
            fn(ExchangeOffice $office) => $this->toResponseDto($office),
            $exchangeOffices
        );
    }
}
