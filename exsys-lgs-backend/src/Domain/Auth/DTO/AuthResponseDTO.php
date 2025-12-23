<?php

namespace App\Domain\Auth\DTO;

class AuthResponseDTO
{
    public string $token;
    public array $user;
    public int $expiresIn;

    public function __construct(
        string $token,
        array $user,
        int $expiresIn = 3600
    ) {
        $this->token = $token;
        $this->user = $user;
        $this->expiresIn = $expiresIn;
    }

// Converts the DTO to an array
    public function toArray(): array
    {
        return [
            'token' => $this->token,
            'user' => $this->user,
            'expires_in' => $this->expiresIn,
            'token_type' => 'Bearer'
        ];
    }
}
