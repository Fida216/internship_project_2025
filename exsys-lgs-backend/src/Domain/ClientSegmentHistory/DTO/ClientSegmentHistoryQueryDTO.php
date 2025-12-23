<?php

namespace App\Domain\ClientSegmentHistory\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class ClientSegmentHistoryQueryDTO
{
    #[Assert\NotBlank(message: 'Client ID is required')]
    #[Assert\Uuid(message: 'Client ID must be a valid UUID')]
    public string $clientId;
}
