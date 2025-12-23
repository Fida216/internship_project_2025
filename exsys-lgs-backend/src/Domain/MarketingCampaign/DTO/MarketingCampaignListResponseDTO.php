<?php

namespace App\Domain\MarketingCampaign\DTO;

class MarketingCampaignListResponseDTO
{
    /** @var MarketingCampaignListItemResponseDTO[] */
    public array $campaigns;
    public int $total;

    public function __construct(array $campaigns)
    {
        $this->campaigns = $campaigns;
        $this->total = count($campaigns);
    }

    public function toArray(): array
    {
        return [
            'campaigns' => array_map(fn($campaign) => $campaign->toArray(), $this->campaigns),
            'total' => $this->total,
        ];
    }
}
