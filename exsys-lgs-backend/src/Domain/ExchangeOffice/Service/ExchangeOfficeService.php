<?php

namespace App\Domain\ExchangeOffice\Service;

use App\Domain\ExchangeOffice\Entity\ExchangeOffice;
use App\Domain\User\Entity\UserInfo;
use App\Shared\Enum\Status;
use App\Shared\Enum\Role;
use App\Domain\ExchangeOffice\DTO\CreateExchangeOfficeDTO;
use App\Domain\ExchangeOffice\DTO\UpdateExchangeOfficeDTO;
use App\Domain\ExchangeOffice\DTO\UpdateExchangeOfficeStatusDTO;
use App\Domain\ExchangeOffice\DTO\ExchangeOfficeResponseDTO;
use App\Domain\ExchangeOffice\DTO\ListExchangeOfficesResponseDTO;
use App\Domain\ExchangeOffice\DTO\ExchangeOfficeWithClientsResponseDTO;
use App\Domain\ExchangeOffice\DTO\AllExchangeOfficesWithClientsResponseDTO;
use App\Domain\ExchangeOffice\Mapper\ExchangeOfficeMapper;
use App\Domain\ExchangeOffice\Repository\ExchangeOfficeRepository;
use App\Domain\User\Repository\UserInfoRepository;
use App\Domain\Client\Repository\ClientRepository;
use App\Domain\Client\Mapper\ClientMapper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ExchangeOfficeService
{
    private EntityManagerInterface $entityManager;
    private ExchangeOfficeRepository $exchangeOfficeRepository;
    private UserInfoRepository $userInfoRepository;
    private ClientRepository $clientRepository;
    private ValidatorInterface $validator;
    private ExchangeOfficeMapper $exchangeOfficeMapper;
    private ClientMapper $clientMapper;

    public function __construct(
        EntityManagerInterface $entityManager,
        ExchangeOfficeRepository $exchangeOfficeRepository,
        UserInfoRepository $userInfoRepository,
        ClientRepository $clientRepository,
        ValidatorInterface $validator,
        ExchangeOfficeMapper $exchangeOfficeMapper,
        ClientMapper $clientMapper
    ) {
        $this->entityManager = $entityManager;
        $this->exchangeOfficeRepository = $exchangeOfficeRepository;
        $this->userInfoRepository = $userInfoRepository;
        $this->clientRepository = $clientRepository;
        $this->validator = $validator;
        $this->exchangeOfficeMapper = $exchangeOfficeMapper;
        $this->clientMapper = $clientMapper;
    }

public function createExchangeOfficeFromDto(CreateExchangeOfficeDTO $dto): ExchangeOfficeResponseDTO
{
    $existingOffice = $this->exchangeOfficeRepository->findOneBy(['email' => $dto->email]);
    if ($existingOffice) {
        throw new \InvalidArgumentException('An exchange office with this email already exists');
    }

    $exchangeOffice = $this->exchangeOfficeMapper->toEntityFromCreateDto($dto);

    $this->entityManager->persist($exchangeOffice);
    $this->entityManager->flush();

    return $this->exchangeOfficeMapper->toResponseDto($exchangeOffice);
}


    public function updateExchangeOfficeFromDto(string $id, UpdateExchangeOfficeDTO $dto): ExchangeOfficeResponseDTO
{

    $exchangeOffice = $this->exchangeOfficeRepository->find($id);
    if (!$exchangeOffice) {
        throw new \InvalidArgumentException('Exchange office not found');
    }

    if (!$dto->hasAnyField()) {
        throw new \InvalidArgumentException('At least one field must be provided for update');
    }

    // Convertir status string → enum si nécessaire
    if (is_string($dto->status)) {
        try {
            $dto->status = Status::from($dto->status);
        } catch (\ValueError) {
            throw new \InvalidArgumentException("Invalid status value: {$dto->status}. Allowed values: " . implode(', ', Status::getAllValues()));
        }
    }

    if ($dto->email !== null && $dto->email !== $exchangeOffice->getEmail()) {
        $existingOffice = $this->exchangeOfficeRepository->findOneBy(['email' => $dto->email]);
        if ($existingOffice) {
            throw new \InvalidArgumentException('An exchange office with this email already exists');
        }
    }

    $this->exchangeOfficeMapper->updateEntityFromDto($exchangeOffice, $dto);

    $this->entityManager->flush();

    return $this->exchangeOfficeMapper->toResponseDto($exchangeOffice);
}




    public function getAllExchangeOfficesWithFilters( array $filters = []): ListExchangeOfficesResponseDTO
    {

   
        $criteria = [];
        

        if (isset($filters['status']) && $filters['status'] !== null) {
            $status = $filters['status'] === 'active' ? Status::ACTIVE : Status::INACTIVE;
            $criteria['officeStatus'] = $status;
        }


        $exchangeOffices = empty($criteria) 
            ? $this->exchangeOfficeRepository->findAll()
            : $this->exchangeOfficeRepository->findBy($criteria);


        $responseDtos = $this->exchangeOfficeMapper->toResponseDtoArray($exchangeOffices);

        return new ListExchangeOfficesResponseDTO($responseDtos, $filters);
    }

    public function getExchangeOfficeDetails(string $id): ExchangeOfficeResponseDTO
    {
        $exchangeOffice = $this->exchangeOfficeRepository->find($id);
        
        if (!$exchangeOffice) {
            throw new \InvalidArgumentException('Exchange office not found');
        }
        return $this->exchangeOfficeMapper->toResponseDto($exchangeOffice);
    }

    public function deleteExchangeOffice(string $id): void
    {


        $exchangeOffice = $this->exchangeOfficeRepository->find($id);
        if (!$exchangeOffice) {
            throw new \InvalidArgumentException('Exchange office not found');
        }


        $hasUsers = $this->userInfoRepository->count(['exchangeOffice' => $exchangeOffice->getId()]) > 0;
        $hasClients = $this->clientRepository->count(['exchangeOffice' => $exchangeOffice->getId()]) > 0;

        if ($hasUsers || $hasClients) {
            throw new \InvalidArgumentException('Cannot delete exchange office: it has associated users or clients');
        }


        $exchangeOffice->setOfficeStatus(Status::INACTIVE);

        $this->entityManager->flush();
    }

    public function getAgentExchangeOffice(UserInfo $currentUser): ExchangeOfficeResponseDTO
    {

        $exchangeOffice = $currentUser->getExchangeOffice();        
        if (!$exchangeOffice) {
            throw new \InvalidArgumentException('Agent is not assigned to any exchange office');
        }


        return $this->exchangeOfficeMapper->toResponseDto($exchangeOffice);
    }




    public function updateExchangeOfficeStatusFromDto(string $exchangeOfficeId, UpdateExchangeOfficeStatusDTO $dto): ExchangeOfficeResponseDTO
{

    $exchangeOffice = $this->exchangeOfficeRepository->find($exchangeOfficeId);
    if (!$exchangeOffice) {
        throw new \InvalidArgumentException('Exchange office not found');
    }

    $exchangeOffice->setOfficeStatus($dto->getStatusEnum());

    $this->entityManager->flush();

    return $this->exchangeOfficeMapper->toResponseDto($exchangeOffice);
}

    public function getAllExchangeOfficesWithClients(UserInfo $currentUser): AllExchangeOfficesWithClientsResponseDTO
    {

        if ($currentUser->getRole() !== Role::ADMIN) {
            throw new \InvalidArgumentException('Only administrators can view all exchange offices with clients');
        }


        $exchangeOffices = $this->exchangeOfficeRepository->findAllOffices();

        $exchangeOfficesWithClients = [];

        foreach ($exchangeOffices as $exchangeOffice) {

            $clients = $this->clientRepository->findBy(['exchangeOffice' => $exchangeOffice]);
            

            $clientDtos = $this->clientMapper->entitiesToResponseDtos($clients);


            $exchangeOfficeWithClients = new ExchangeOfficeWithClientsResponseDTO(
                $exchangeOffice->getId()->toString(),
                $exchangeOffice->getName(),
                $exchangeOffice->getAddress(),
                $exchangeOffice->getEmail(),
                $exchangeOffice->getPhone(),
                $exchangeOffice->getOwner(),
                $exchangeOffice->getOfficeStatus()->value,
                $exchangeOffice->getCreatedAt()->format('Y-m-d H:i:s'),
                $clientDtos,
                count($clientDtos)
            );

            $exchangeOfficesWithClients[] = $exchangeOfficeWithClients;
        }

        return new AllExchangeOfficesWithClientsResponseDTO($exchangeOfficesWithClients);
    }

}
