<?php

namespace App\Domain\QuickMessage\DTO;

use App\Domain\QuickMessage\Entity\QuickMessage;

class QuickMessageResponseDTO
{
    public string $id;
    public string $title;
    public string $channelType;
    public string $content;
    /** @var QuickMessageClientResponseDTO[] */
    public array $clients = [];

    public function __construct(QuickMessage $quickMessage)
    {
        $this->id = $quickMessage->getId()->toString();
        $this->title = $quickMessage->getTitle();
        $this->channelType = $quickMessage->getChannelType()->value;
        $this->content = $quickMessage->getContent();
        
        foreach ($quickMessage->getTargetClients() as $target) {
            $this->clients[] = new QuickMessageClientResponseDTO($target->getClient());
        }
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'channelType' => $this->channelType,
            'content' => $this->content,
            'clients' => array_map(fn($client) => $client->toArray(), $this->clients)
        ];
    }
}
