<?php

namespace App\Domain\MarketingAction\DTO;

use App\Domain\MarketingAction\Entity\MarketingAction;
use App\Domain\MarketingCampaign\DTO\CampaignClientResponseDTO;

class MarketingActionResponseDTO
{
    public string $id;
    public string $title;
    public string $channelType;
    public string $content;
    public string $campaignId;
    public string $campaignTitle;
    /** @var CampaignClientResponseDTO[] */
    public array $clients = [];

    public function __construct(MarketingAction $marketingAction)
    {
        $this->id = $marketingAction->getId()->toString();
        $this->title = $marketingAction->getTitle();
        $this->channelType = $marketingAction->getChannelType()->value;
        $this->content = $marketingAction->getContent();
        $this->campaignId = $marketingAction->getCampaign()->getId()->toString();
        $this->campaignTitle = $marketingAction->getCampaign()->getTitle();
        
        foreach ($marketingAction->getTargetClients() as $target) {
            $this->clients[] = new CampaignClientResponseDTO($target->getClient());
        }
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
            'clients' => array_map(fn($client) => $client->toArray(), $this->clients)
        ];
    }
}
