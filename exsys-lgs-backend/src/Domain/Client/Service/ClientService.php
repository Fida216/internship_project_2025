<?php

namespace App\Domain\Client\Service;

use App\Domain\Client\Entity\Client;
use App\Domain\User\Entity\UserInfo;
use App\Shared\Enum\Status;
use App\Shared\Enum\Role;
use App\Shared\Enum\Gender;
use App\Shared\Enum\AcquisitionSource;
use App\Domain\Country\Service\CountryService;
use App\Domain\Client\Repository\ClientRepository;
use App\Domain\Client\DTO\CreateClientDTO;
use App\Domain\ClientSegmentHistory\Entity\ClientSegmentHistory;
use App\Domain\Client\DTO\UpdateClientDTO;
use App\Domain\Client\DTO\ClientResponseDTO;
use App\Domain\Client\DTO\ClientsFilterDTO;
use App\Domain\Client\DTO\ListClientsResponseDTO;
use App\Domain\Client\DTO\AdminClientsFilterDTO;
use App\Domain\Client\Mapper\ClientMapper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ClientService
{
    private EntityManagerInterface $entityManager;
    private ClientRepository $clientRepository;
    private ValidatorInterface $validator;
    private ClientMapper $clientMapper;
    private CountryService $countryService;

    public function __construct(
        EntityManagerInterface $entityManager,
        ClientRepository $clientRepository,
        ValidatorInterface $validator,
        ClientMapper $clientMapper,
        CountryService $countryService
    ) {
        $this->entityManager = $entityManager;
        $this->clientRepository = $clientRepository;
        $this->validator = $validator;
        $this->clientMapper = $clientMapper;
        $this->countryService = $countryService;
    }

    public function createClientFromDTO(CreateClientDTO $dto, UserInfo $currentUser): ClientResponseDTO
    {
        $exchangeOfficeId = $currentUser->getExchangeOffice()->getId();

        // Unicité par passeport
        if (!empty($dto->passport)) {
            $existingByPassport = $this->clientRepository->findByPassportInExchangeOffice($dto->passport, $exchangeOfficeId);
            if ($existingByPassport) {
                throw new \InvalidArgumentException('Un client avec ce numéro de passeport existe déjà dans ce bureau de change.');
            }
        }

        // Unicité par carte d'identité
        if (!empty($dto->nationalId)) {
            $existingByNationalId = $this->clientRepository->findByNationalIdInExchangeOffice($dto->nationalId, $exchangeOfficeId);
            if ($existingByNationalId) {
                throw new \InvalidArgumentException('Un client avec ce numéro de pièce d\'identité existe déjà dans ce bureau de change.');
            }
        }
        

        // Mapping DTO → Entity
        $client = $this->clientMapper->createDtoToEntity($dto, $exchangeOfficeId);

        // Persist client
        $this->entityManager->persist($client);
        $this->entityManager->flush();
        // Record initial segment history if provided
        if ($dto->currentSegment) {
            $history = new ClientSegmentHistory();
            $history->setClient($client)
                    ->setSegment($dto->currentSegment)
                    ->setCreatedAt(new \DateTime());
            $this->entityManager->persist($history);
            $this->entityManager->flush();
        }

        return $this->clientMapper->entityToResponseDto($client);
    }


    public function updateClientFromDTO(string $clientId, UpdateClientDTO $updateDto, UserInfo $currentUser): ClientResponseDTO
    {
        $client = $this->findClientById($clientId);
        $this->validateClientUpdatePermissions($currentUser, $client);
        $exchangeOfficeId = $currentUser->getExchangeOffice()->getId();

       // Unicité par passeport
        if (!empty($updateDto->passport)) {
            $existingByPassport = $this->clientRepository->findByPassportInExchangeOffice($updateDto->passport, $exchangeOfficeId);
            if ($existingByPassport) {
                throw new \InvalidArgumentException('Un client avec ce numéro de passeport existe déjà dans ce bureau de change.');
            }
        }

        // Unicité par carte d'identité
        if (!empty($updateDto->nationalId)) {
            $existingByNationalId = $this->clientRepository->findByNationalIdInExchangeOffice($updateDto->nationalId, $exchangeOfficeId);
            if ($existingByNationalId) {
                throw new \InvalidArgumentException('Un client avec ce numéro de pièce d\'identité existe déjà dans ce bureau de change.');
            }
        }

        $this->clientMapper->updateEntityFromDto($client, $updateDto);
        // Persist client changes
        $this->entityManager->flush();
        // Record segment history if segment was updated
        if ($updateDto->currentSegment !== null) {
            $history = new ClientSegmentHistory();
            $history->setClient($client)
                    ->setSegment($updateDto->currentSegment)
                    ->setCreatedAt(new \DateTime());
            $this->entityManager->persist($history);
            $this->entityManager->flush();
        }
        return $this->clientMapper->entityToResponseDto($client);
    }


    public function deleteClient(string $clientId, UserInfo $currentUser): void
    {
        $client = $this->findClientById($clientId);
        $this->validateClientDeletePermissions($currentUser, $client);


        $client->setStatus(Status::INACTIVE);
        
        $this->entityManager->flush();
    }

    public function findClientById(string $clientId): Client
    {
        $client = $this->clientRepository->find($clientId);
        if (!$client) {
            throw new \InvalidArgumentException('Client not found');
        }
        return $client;
    }

    public function getClientDetails(string $id, UserInfo $currentUser): ClientResponseDTO
    {
        $client = $this->findClientById($id);
        $this->validateClientAccessPermissions($currentUser, $client);
        

        return $this->clientMapper->entityToResponseDto($client);
    }

    public function getAllClients(UserInfo $currentUser, array $filters = []): array
    {
        if ($currentUser->getRole() !== Role::ADMIN) {
            throw new \InvalidArgumentException('Only administrators can view all clients');
        }

        $criteria = [];
        if (isset($filters['status'])) {
            $criteria['status'] = $filters['status'];
        }

        return $this->clientRepository->findBy($criteria);
    }


public function getMyOfficeClients(UserInfo $currentUser, ClientsFilterDTO $filterDto): ListClientsResponseDTO
    {


        $exchangeOfficeId = $currentUser->getExchangeOffice()->getId();

        $limit = $filterDto->limit;
        $offset = ($filterDto->page - 1) * $limit;


        $clients = $this->clientRepository->findByExchangeOfficeWithFilters(
            $exchangeOfficeId,
            $filterDto->status ?: null,
            $filterDto->search ?: null,
            $filterDto->limit,
            $offset,
            $filterDto->nationality ?: null,
            $filterDto->gender ?: null,
            $filterDto->acquisitionSource ?: null,
            $filterDto->currentSegment ?: null
        );


        $totalClients = $this->clientRepository->countByExchangeOfficeWithFilters(
            $exchangeOfficeId,
            $filterDto->status ?: null,
            $filterDto->search ?: null,
            $filterDto->nationality ?: null,
            $filterDto->gender ?: null,
            $filterDto->acquisitionSource ?: null,
            $filterDto->currentSegment ?: null
        );


        $responseDtos = $this->clientMapper->entitiesToResponseDtos($clients);


        $totalPages = (int) ceil($totalClients / $limit);
        $response = new ListClientsResponseDTO($responseDtos);
        

        $response->totalClients = $totalClients;
        $response->totalPages = $totalPages;
        $response->currentPage = $filterDto->page;
        $response->hasNextPage = $filterDto->page < $totalPages;
        $response->hasPreviousPage = $filterDto->page > 1;

        return $response;
    }

public function getAllClientsForAdmin(AdminClientsFilterDTO $filterDto): ListClientsResponseDTO
    {
        $limit = $filterDto->limit;
        $offset = ($filterDto->page - 1) * $limit;

        $clients = $this->clientRepository->findAllWithFilters(
            $filterDto->status ?: null,
            $filterDto->search ?: null,
            $limit,
            $offset,
            $filterDto->nationality ?: null,
            $filterDto->gender ?: null,
            $filterDto->exchangeOfficeId ?: null,
            $filterDto->acquisitionSource ?: null,
            $filterDto->currentSegment ?: null
        );


        $totalClients = $this->clientRepository->countAllWithFilters(
            $filterDto->status ?: null,
            $filterDto->search ?: null,
            $filterDto->nationality ?: null,
            $filterDto->gender ?: null,
            $filterDto->exchangeOfficeId ?: null,
            $filterDto->acquisitionSource ?: null,
            $filterDto->currentSegment ?: null
        );


        $responseDtos = $this->clientMapper->entitiesToResponseDtos($clients);


        $totalPages = (int) ceil($totalClients / $limit);
        $response = new ListClientsResponseDTO($responseDtos);
        

        $response->totalClients = $totalClients;
        $response->totalPages = $totalPages;
        $response->currentPage = $filterDto->page;
        $response->hasNextPage = $filterDto->page < $totalPages;
        $response->hasPreviousPage = $filterDto->page > 1;

        return $response;
    }


    private function validateClientUpdatePermissions(UserInfo $currentUser, Client $client): void
    {
        if ($currentUser->getRole() === Role::AGENT && 
            $currentUser->getExchangeOffice()->getId() !== $client->getExchangeOffice()->getId()) {
            throw new \InvalidArgumentException('You can only update clients from your own exchange office');
        }
    }

    private function validateClientDeletePermissions(UserInfo $currentUser, Client $client): void
    {
        if ($currentUser->getRole() === Role::AGENT && 
            $currentUser->getExchangeOffice()->getId() !== $client->getExchangeOffice()->getId()) {
            throw new \InvalidArgumentException('You can only delete clients from your own exchange office');
        }
    }


    private function validateClientAccessPermissions(UserInfo $currentUser, Client $client): void
    {
        if ($currentUser->getRole() === Role::AGENT && 
            $currentUser->getExchangeOffice()->getId() !== $client->getExchangeOffice()->getId()) {
            throw new \InvalidArgumentException('You can only view clients from your own exchange office');
        }
    }
}
