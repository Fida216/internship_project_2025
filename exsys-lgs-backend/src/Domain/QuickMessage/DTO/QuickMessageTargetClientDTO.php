<?php

namespace App\Domain\QuickMessage\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class QuickMessageTargetClientDTO
{
    #[Assert\NotBlank(message: 'Client ID is required')]
    #[Assert\Uuid(message: 'Client ID must be a valid UUID')]
    public string $clientId;
}
