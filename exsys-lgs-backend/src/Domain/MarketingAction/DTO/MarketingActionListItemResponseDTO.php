<?php

namespace App\Domain\MarketingAction\DTO;

use App\Domain\MarketingAction\Entity\MarketingAction;

class MarketingActionListItemResponseDTO
{
    public string $id;
    public string $title;
    public string $channelType;
    public string $content;
    public string $campaignId;
    public string $campaignTitle;
    public int $clientCount;
    public string $createdAt;

    public function __construct(MarketingAction $marketingAction)
    {
        $this->id = $marketingAction->getId()->toString();
        $this->title = $marketingAction->getTitle();
        $this->channelType = $marketingAction->getChannelType()->value;
        $this->content = $marketingAction->getContent();
        $this->campaignId = $marketingAction->getCampaign()->getId()->toString();
        $this->campaignTitle = $marketingAction->getCampaign()->getTitle();
        $this->clientCount = $marketingAction->getTargetClients()->count();
        $this->createdAt = $marketingAction->getCreatedAt() ? $marketingAction->getCreatedAt()->format('Y-m-d H:i:s') : '';
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'channelType' => $this->channelType,
            'content' => $this->content,
            'campaignId' => $this->campaignId,
            'campaignTitle' => $this->campaignTitle,
            'clientCount' => $this->clientCount,
            'createdAt' => $this->createdAt,
        ];
    }
}
