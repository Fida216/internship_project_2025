<?php

namespace App\Domain\MarketingCampaign\Service;

use App\Domain\MarketingCampaign\DTO\CreateMarketingCampaignDTO;
use App\Domain\MarketingCampaign\DTO\MarketingCampaignCreateResponseDTO;
use App\Domain\MarketingCampaign\DTO\MarketingCampaignResponseDTO;
use App\Domain\MarketingCampaign\DTO\MarketingCampaignListResponseDTO;
use App\Domain\MarketingCampaign\DTO\MarketingCampaignListItemResponseDTO;
use App\Domain\MarketingCampaign\DTO\ManageTargetClientsDTO;
use App\Domain\MarketingCampaign\Entity\MarketingCampaign;
use App\Domain\MarketingCampaign\Entity\CampaignTargetClient;
use App\Domain\MarketingCampaign\Repository\MarketingCampaignRepository;
use App\Domain\MarketingCampaign\Mapper\MarketingCampaignMapper;
use App\Domain\MarketingAction\Repository\MarketingActionRepository;
use App\Domain\Client\Repository\ClientRepository;
use App\Domain\User\Entity\UserInfo;
use App\Shared\Enum\CampaignStatus;
use App\Shared\Exception\Business\BusinessException;
use Doctrine\ORM\EntityManagerInterface;

class MarketingCampaignService
{
    public function __construct(
        private MarketingCampaignRepository $campaignRepository,
        private ClientRepository $clientRepository,
        private EntityManagerInterface $entityManager,
        private MarketingActionRepository $marketingActionRepository
    ) {}

    public function createMarketingCampaignFromDto(
        CreateMarketingCampaignDTO $dto, 
        UserInfo $currentUser
    ): MarketingCampaignCreateResponseDTO {
        
        // Validate user has exchange office
        $exchangeOffice = $currentUser->getExchangeOffice();
        if (!$exchangeOffice) {
            throw new \InvalidArgumentException('User must be assigned to an exchange office');
        }

        // Validate dates
        $startDate = new \DateTime($dto->startDate);
        $endDate = new \DateTime($dto->endDate);
        if ($startDate >= $endDate) {
            throw new \InvalidArgumentException('End date must be after start date');
        }

        // Get client IDs from targets
        $clientIds = array_map(fn($target) => $target->clientId, $dto->targets);
        
        // Verify all clients exist and belong to the user's exchange office
        $clients = $this->clientRepository->findBy([
            'id' => $clientIds,
            'exchangeOffice' => $exchangeOffice
        ]);

        if (count($clients) !== count($clientIds)) {
            throw new \InvalidArgumentException('One or more clients not found or do not belong to your exchange office');
        }

        // Create the marketing campaign
        $campaign = MarketingCampaignMapper::fromCreateDTO($dto, $exchangeOffice, $currentUser, $clients);
        
        // Persist the campaign first
        $this->entityManager->persist($campaign);
        $this->entityManager->flush();

        // Create target client relationships
        foreach ($clients as $client) {
            $targetClient = new CampaignTargetClient();
            $targetClient->setMarketingCampaign($campaign);
            $targetClient->setClient($client);
            $targetClient->setAddedAt(new \DateTime());
            
            $this->entityManager->persist($targetClient);
        }

        $this->entityManager->flush();

        return MarketingCampaignMapper::toCreateResponseDTO($campaign);
    }

    public function getMarketingCampaignById(string $campaignId, UserInfo $currentUser): MarketingCampaignResponseDTO
    {
        $exchangeOffice = $currentUser->getExchangeOffice();
        if (!$exchangeOffice) {
            throw new \InvalidArgumentException('User must be assigned to an exchange office');
        }

        $campaign = $this->campaignRepository->find($campaignId);
        if (!$campaign) {
            throw new \InvalidArgumentException('Marketing campaign not found');
        }

        // Verify campaign belongs to user's exchange office
        if ($campaign->getExchangeOffice()->getId() !== $exchangeOffice->getId()) {
            throw new \InvalidArgumentException('Access denied: campaign does not belong to your exchange office');
        }

        // Get marketing actions for this campaign
        $marketingActions = $this->marketingActionRepository->findByCampaign($campaignId);

        return MarketingCampaignMapper::toResponseDTO($campaign, $marketingActions);
    }

    public function getAllMarketingCampaignsForExchangeOffice(UserInfo $currentUser): MarketingCampaignListResponseDTO
    {
        $exchangeOffice = $currentUser->getExchangeOffice();
        if (!$exchangeOffice) {
            throw new \InvalidArgumentException('User must be assigned to an exchange office');
        }

        $campaigns = $this->campaignRepository->findByExchangeOffice($exchangeOffice->getId());
        
        $campaignDTOs = [];
        foreach ($campaigns as $campaign) {
            $campaignDTOs[] = new MarketingCampaignListItemResponseDTO($campaign);
        }

        return new MarketingCampaignListResponseDTO($campaignDTOs);
    }

    public function updateCampaignStatusById(
        string $campaignId,
        CampaignStatus $status, 
        UserInfo $currentUser
    ): MarketingCampaignResponseDTO {
        // Récupérer la campagne
        $campaign = $this->campaignRepository->findOneBy(['id' => $campaignId]);
        
        if (!$campaign) {
            throw new BusinessException('Campaign not found');
        }

        // Vérifier que l'agent appartient au même exchange office que la campagne
        if ($campaign->getExchangeOffice()->getId() !== $currentUser->getExchangeOffice()->getId()) {
            throw new BusinessException('Access denied: You can only modify campaigns from your exchange office');
        }

        // Mettre à jour le statut
        $campaign->setStatus($status);
        // Persister les changements
        $this->entityManager->flush();

        return MarketingCampaignMapper::toResponseDTO($campaign);
    }

    public function addTargetClients(string $campaignId, ManageTargetClientsDTO $dto, UserInfo $currentUser): MarketingCampaignResponseDTO
    {
        // Validate user has exchange office
        $exchangeOffice = $currentUser->getExchangeOffice();
        if (!$exchangeOffice) {
            throw new \InvalidArgumentException('User must be assigned to an exchange office');
        }

        // Get the campaign
        $campaign = $this->campaignRepository->findOneBy(['id' => $campaignId]);
        if (!$campaign) {
            throw new \InvalidArgumentException('Marketing campaign not found');
        }

        // Verify campaign belongs to user's exchange office
        if ($campaign->getExchangeOffice()->getId() !== $exchangeOffice->getId()) {
            throw new \InvalidArgumentException('Access denied: campaign does not belong to your exchange office');
        }

        // Verify all clients exist and belong to the user's exchange office
        $clients = $this->clientRepository->findBy([
            'id' => $dto->clientIds,
            'exchangeOffice' => $exchangeOffice
        ]);

        if (count($clients) !== count($dto->clientIds)) {
            throw new \InvalidArgumentException('Some clients not found or do not belong to your exchange office');
        }

        // Get existing target client IDs to avoid duplicates
        $existingTargetIds = $campaign->getTargetClients()->map(fn($target) => $target->getClient()->getId()->toString())->toArray();

        // Add new target clients (only if not already exists)
        foreach ($clients as $client) {
            $clientId = $client->getId()->toString();
            if (!in_array($clientId, $existingTargetIds)) {
                $targetClient = new CampaignTargetClient();
                $targetClient->setMarketingCampaign($campaign);
                $targetClient->setClient($client);
                $targetClient->setAddedAt(new \DateTime());
                // Add to collection manually instead of using the entity method
                $campaign->getTargetClients()->add($targetClient);
                $this->entityManager->persist($targetClient);
            }
        }

        $this->entityManager->flush();

        return MarketingCampaignMapper::toResponseDTO($campaign);
    }

    public function removeTargetClients(string $campaignId, ManageTargetClientsDTO $dto, UserInfo $currentUser): MarketingCampaignResponseDTO
    {
        // Validate user has exchange office
        $exchangeOffice = $currentUser->getExchangeOffice();
        if (!$exchangeOffice) {
            throw new \InvalidArgumentException('User must be assigned to an exchange office');
        }

        // Get the campaign
        $campaign = $this->campaignRepository->findOneBy(['id' => $campaignId]);
        if (!$campaign) {
            throw new \InvalidArgumentException('Marketing campaign not found');
        }

        // Verify campaign belongs to user's exchange office
        if ($campaign->getExchangeOffice()->getId() !== $exchangeOffice->getId()) {
            throw new \InvalidArgumentException('Access denied: campaign does not belong to your exchange office');
        }

        // Remove target clients
        $targetClients = $campaign->getTargetClients()->toArray();
        foreach ($targetClients as $targetClient) {
            $clientId = $targetClient->getClient()->getId()->toString();
            if (in_array($clientId, $dto->clientIds)) {
                $campaign->getTargetClients()->removeElement($targetClient);
                $this->entityManager->remove($targetClient);
            }
        }

        $this->entityManager->flush();

        return MarketingCampaignMapper::toResponseDTO($campaign);
    }
}
