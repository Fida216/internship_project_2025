<?php

namespace App\Domain\ClientSegmentHistory\Service;

use App\Domain\ClientSegmentHistory\Repository\ClientSegmentHistoryRepository;
use App\Domain\Client\Repository\ClientRepository;
use App\Domain\User\Entity\UserInfo;
use App\Shared\Enum\Role;
use App\Domain\ClientSegmentHistory\Entity\ClientSegmentHistory;
use App\Domain\ClientSegmentHistory\Mapper\ClientSegmentHistoryResponseMapper;
use App\Domain\ClientSegmentHistory\DTO\ClientSegmentHistoryListResponseDTO;
use DateTime;

class ClientSegmentHistoryService
{
    private ClientSegmentHistoryRepository $historyRepository;
    private ClientRepository $clientRepository;
    private ClientSegmentHistoryResponseMapper $responseMapper;

    public function __construct(
        ClientSegmentHistoryRepository $historyRepository,
        ClientRepository $clientRepository,
        ClientSegmentHistoryResponseMapper $responseMapper
    ) {
        $this->historyRepository = $historyRepository;
        $this->clientRepository = $clientRepository;
        $this->responseMapper = $responseMapper;
    }

    /**
     * Get segment history as response DTO for a client based on user role
     *
     * @param UserInfo $user
     * @param string $clientId
     * @return ClientSegmentHistoryListResponseDTO
     */
    public function getSegmentHistory(UserInfo $user, string $clientId): ClientSegmentHistoryListResponseDTO
    {
        $client = $this->clientRepository->find($clientId);
        if (!$client) {
            throw new \InvalidArgumentException('Client not found');
        }

        // Agents can only view clients of their exchange office
        if ($user->getRole() === Role::AGENT) {
            $agentOffice = $user->getExchangeOffice();
            if (!$agentOffice || $client->getExchangeOffice()->getId()->toString() !== $agentOffice->getId()->toString()) {
                throw new \InvalidArgumentException('You can only view segment history for clients of your exchange office');
            }
        }

        $histories = $this->historyRepository->findByClientId($clientId);
        $mapped = $this->responseMapper->mapList($histories);
        return new ClientSegmentHistoryListResponseDTO(
            'Client segment history retrieved successfully',
            $mapped
        );
    }
}
