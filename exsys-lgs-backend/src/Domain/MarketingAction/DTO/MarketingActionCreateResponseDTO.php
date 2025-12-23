<?php

namespace App\Domain\MarketingAction\DTO;

class MarketingActionCreateResponseDTO
{
    public string $id;
    public string $message;

    public function __construct(string $id, string $message = 'Marketing action created successfully')
    {
        $this->id = $id;
        $this->message = $message;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'message' => $this->message,
        ];
    }
}
