<?php

namespace App\Domain\MarketingAction\Service;

use App\Domain\MarketingAction\DTO\MarketingActionCreateDTO;
use App\Domain\MarketingAction\DTO\MarketingActionResponseDTO;
use App\Domain\MarketingAction\DTO\MarketingActionCreateResponseDTO;
use App\Domain\MarketingAction\DTO\MarketingActionListResponseDTO;
use App\Domain\MarketingAction\DTO\MarketingActionListItemResponseDTO;
use App\Domain\MarketingAction\DTO\MarketingActionWithCampaignListResponseDTO;
use App\Domain\MarketingAction\DTO\MarketingActionSimpleResponseDTO;
use App\Domain\MarketingAction\Entity\MarketingAction;
use App\Domain\MarketingAction\Mapper\MarketingActionMapper;
use App\Domain\MarketingAction\Repository\MarketingActionRepository;
use App\Domain\MarketingCampaign\Repository\MarketingCampaignRepository;
use App\Domain\Client\Repository\ClientRepository;
use App\Shared\Service\CommunicationService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class MarketingActionService
{
    public function __construct(
        private MarketingActionRepository $marketingActionRepository,
        private MarketingCampaignRepository $campaignRepository,
        private ClientRepository $clientRepository,
        private EntityManagerInterface $em,
        private CommunicationService $communicationService,
        private LoggerInterface $logger
    ) {}

    public function getMarketingActionForAgent(string $actionId, $agent = null): MarketingActionResponseDTO
    {

        $exchangeOffice = $agent->getExchangeOffice();
        if (!$exchangeOffice) {
            throw new \InvalidArgumentException('Agent does not have an exchange office');
        }
        $marketingAction = $this->marketingActionRepository->find($actionId);
        if (!$marketingAction) {
            throw new \InvalidArgumentException('MarketingAction not found');
        }
        // Verify that the marketing action belongs to the agent's exchange office through the campaign
        if ($marketingAction->getCampaign()->getExchangeOffice()->getId() != $exchangeOffice->getId()) {
            throw new \InvalidArgumentException('Access denied: not your exchange office');
        }
        
        return new MarketingActionResponseDTO($marketingAction);
    }

    public function createMarketingActionForAgent(MarketingActionCreateDTO $dto, $agent = null): MarketingActionCreateResponseDTO
    {
        // Get the agent's exchange office
        $exchangeOffice = $agent->getExchangeOffice();
        if (!$exchangeOffice) {
            throw new \InvalidArgumentException('Agent does not have an exchange office');
        }

        // Verify that the campaign exists and belongs to the agent's exchange office
        $campaign = $this->campaignRepository->find($dto->campaignId);
        if (!$campaign) {
            throw new \InvalidArgumentException('Marketing campaign not found');
        }

        if ($campaign->getExchangeOffice()->getId() != $exchangeOffice->getId()) {
            throw new \InvalidArgumentException('Campaign does not belong to your exchange office');
        }

        // Create the marketing action (targets come from campaign)
        $marketingAction = MarketingActionMapper::fromCreateDTO($dto, $campaign);
        $marketingAction->setUser($agent);
        $this->em->persist($marketingAction);
        $this->em->flush();

        $statusMessage = 'Marketing action created successfully.';
        switch ($marketingAction->getChannelType()->value) {
            case 'email':
                $emailSent = $this->communicationService->sendEmail(
                    $marketingAction->getTitle(),
                    $marketingAction->getContent(),
                    $marketingAction->getTargetClients(),
                    $marketingAction->getId()->toString()
                );
                $statusMessage .= $emailSent ? ' Email sent.' : ' Email failed to send.';
                break;
            case 'sms':
                $this->communicationService->sendSms(
                    $marketingAction->getContent(),
                    $marketingAction->getTargetClients()
                );
                $statusMessage .= ' SMS sent.';
                break;
            case 'whatsapp':
                $this->communicationService->sendWhatsapp(
                    $marketingAction->getContent(),
                    $marketingAction->getTargetClients()
                );
                $statusMessage .= ' WhatsApp sent.';
                break;
        }
        
        return new MarketingActionCreateResponseDTO($marketingAction->getId()->toString(), $statusMessage);
    }

    public function getMarketingActionsByCampaign(string $campaignId, $agent = null): MarketingActionWithCampaignListResponseDTO
    {
        $exchangeOffice = $agent->getExchangeOffice();
        if (!$exchangeOffice) {
            throw new \InvalidArgumentException('Agent does not have an exchange office');
        }

        // Verify that the campaign belongs to the agent's exchange office
        $campaign = $this->campaignRepository->find($campaignId);
        if (!$campaign) {
            throw new \InvalidArgumentException('Marketing campaign not found');
        }

        if ($campaign->getExchangeOffice()->getId() != $exchangeOffice->getId()) {
            throw new \InvalidArgumentException('Campaign does not belong to your exchange office');
        }

        // Get marketing actions for this campaign
        $actions = $this->marketingActionRepository->findByCampaign($campaignId);
        
        $actionDTOs = [];
        foreach ($actions as $marketingAction) {
            $actionDTOs[] = new MarketingActionSimpleResponseDTO($marketingAction);
        }
        return new MarketingActionWithCampaignListResponseDTO($campaign, $actionDTOs);
    }
}
