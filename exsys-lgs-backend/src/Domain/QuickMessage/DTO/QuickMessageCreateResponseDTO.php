<?php

namespace App\Domain\QuickMessage\DTO;

class QuickMessageCreateResponseDTO
{
    public string $id;
    public string $message;

    public function __construct(string $id, string $message = 'Quick message created successfully')
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
