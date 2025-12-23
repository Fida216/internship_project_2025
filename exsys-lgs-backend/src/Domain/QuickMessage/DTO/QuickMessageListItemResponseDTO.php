<?php

namespace App\Domain\QuickMessage\DTO;

use App\Domain\QuickMessage\Entity\QuickMessage;

class QuickMessageListItemResponseDTO
{
    public string $id;
    public string $title;
    public string $channelType;
    public string $content;
    public int $clientCount;
    public string $createdAt;

    public function __construct(QuickMessage $quickMessage)
    {
        $this->id = $quickMessage->getId()->toString();
        $this->title = $quickMessage->getTitle();
        $this->channelType = $quickMessage->getChannelType()->value;
        $this->content = $quickMessage->getContent();
        $this->clientCount = $quickMessage->getTargetClients()->count();
        $this->createdAt = $quickMessage->getCreatedAt() ? $quickMessage->getCreatedAt()->format('Y-m-d H:i:s') : '';
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'channelType' => $this->channelType,
            'content' => $this->content,
            'clientCount' => $this->clientCount,
            'createdAt' => $this->createdAt,
        ];
    }
}
