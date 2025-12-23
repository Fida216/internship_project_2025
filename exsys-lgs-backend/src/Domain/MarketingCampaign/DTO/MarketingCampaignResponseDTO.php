<?php

namespace App\Domain\MarketingCampaign\DTO;

use App\Domain\MarketingCampaign\Entity\MarketingCampaign;
use App\Domain\MarketingAction\DTO\MarketingActionSimpleResponseDTO;

class MarketingCampaignResponseDTO
{
    public string $id;
    public string $title;
    public string $description;
    public string $status;
    public string $startDate;
    public string $endDate;
    public string $createdAt;
    /** @var CampaignClientResponseDTO[] */
    public array $targetClients = [];
    /** @var MarketingActionSimpleResponseDTO[] */
    public array $marketingActions = [];

    public function __construct(MarketingCampaign $campaign, array $marketingActions = [])
    {
        $this->id = $campaign->getId()->toString();
        $this->title = $campaign->getTitle();
        $this->description = $campaign->getDescription();
        $this->status = $campaign->getStatus()->value;
        $this->startDate = $campaign->getStartDate()->format('Y-m-d');
        $this->endDate = $campaign->getEndDate()->format('Y-m-d');
        $this->createdAt = $campaign->getCreatedAt()->format('Y-m-d H:i:s');
        
        // Map target clients
        foreach ($campaign->getTargetClients() as $targetClient) {
            $this->targetClients[] = new CampaignClientResponseDTO($targetClient->getClient());
        }
        
        // Map marketing actions
        foreach ($marketingActions as $action) {
            $this->marketingActions[] = new MarketingActionSimpleResponseDTO($action);
        }
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'createdAt' => $this->createdAt,
            'targetClients' => array_map(fn($client) => $client->toArray(), $this->targetClients),
            'marketingActions' => array_map(fn($action) => $action->toArray(), $this->marketingActions)
        ];
    }
}
