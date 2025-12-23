<?php

namespace App\Domain\Auth\DTO;

class CurrentUserResponseDTO
{
    public array $user;

    public function __construct(array $user)
    {
        $this->user = $user;
    }


// Converts the DTO to an array
     
    public function toArray(): array
    {
        return [
            'user' => $this->user
        ];
    }
}
