<?php

namespace App\Domain\Client\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class ClientIDQueryDTO
{
    #[Assert\NotBlank(message: 'Client ID is required')]
    #[Assert\Uuid(message: 'Client ID must be a valid UUID')]
    public string $clientId;
}
