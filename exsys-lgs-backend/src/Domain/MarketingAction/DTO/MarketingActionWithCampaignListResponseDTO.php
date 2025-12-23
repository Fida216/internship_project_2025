<?php

namespace App\Domain\MarketingAction\DTO;

use App\Domain\MarketingCampaign\Entity\MarketingCampaign;

class MarketingActionWithCampaignListResponseDTO
{
    public array $marketingCampaign;
    /** @var MarketingActionSimpleResponseDTO[] */
    public array $marketingActions;
    public int $total;

    public function __construct(MarketingCampaign $campaign, array $marketingActions)
    {
        $this->marketingCampaign = [
            'id' => $campaign->getId()->toString(),
            'title' => $campaign->getTitle(),
            'description' => $campaign->getDescription(),
            'startDate' => $campaign->getStartDate()->format('Y-m-d'),
            'endDate' => $campaign->getEndDate()->format('Y-m-d'),
            'status' => $campaign->getStatus()->value,
            'createdAt' => $campaign->getCreatedAt()->format('Y-m-d H:i:s'),
        ];
        $this->marketingActions = $marketingActions;
        $this->total = count($marketingActions);
    }

    public function toArray(): array
    {
        return [
            'marketingCampaign' => $this->marketingCampaign,
            'marketingActions' => array_map(fn($action) => $action->toArray(), $this->marketingActions),
            'total' => $this->total,
        ];
    }
}
