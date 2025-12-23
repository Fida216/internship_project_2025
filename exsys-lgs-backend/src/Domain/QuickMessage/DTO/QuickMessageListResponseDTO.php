<?php

namespace App\Domain\QuickMessage\DTO;

class QuickMessageListResponseDTO
{
    /** @var QuickMessageListItemResponseDTO[] */
    public array $quickMessages;
    public int $total;

    public function __construct(array $quickMessages)
    {
        $this->quickMessages = $quickMessages;
        $this->total = count($quickMessages);
    }

    public function toArray(): array
    {
        return [
            'quickMessages' => array_map(fn($message) => $message->toArray(), $this->quickMessages),
            'total' => $this->total,
        ];
    }
}
