<?php

namespace App\Domain\User\DTO;

class PasswordChangeResponseDTO
{
    public string $message;

    public function __construct(string $message)
    {
        $this->message = $message;
    }

    public function toArray(): array
    {
        return [
            'message' => $this->message,
        ];
    }
}
