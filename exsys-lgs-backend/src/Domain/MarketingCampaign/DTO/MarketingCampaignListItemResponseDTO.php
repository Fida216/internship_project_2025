<?php

namespace App\Domain\MarketingCampaign\DTO;

use App\Domain\MarketingCampaign\Entity\MarketingCampaign;

class MarketingCampaignListItemResponseDTO
{
    public string $id;
    public string $title;
    public string $status;
    public string $startDate;
    public string $endDate;
    public int $targetClientCount;
    public string $createdAt;

    public function __construct(MarketingCampaign $campaign)
    {
        $this->id = $campaign->getId()->toString();
        $this->title = $campaign->getTitle();
        $this->status = $campaign->getStatus()->value;
        $this->startDate = $campaign->getStartDate()->format('Y-m-d');
        $this->endDate = $campaign->getEndDate()->format('Y-m-d');
        $this->targetClientCount = $campaign->getTargetClients()->count();
        $this->createdAt = $campaign->getCreatedAt()->format('Y-m-d H:i:s');
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'status' => $this->status,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'targetClientCount' => $this->targetClientCount,
            'createdAt' => $this->createdAt,
        ];
    }
}
