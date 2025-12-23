<?php

namespace App\Domain\MarketingCampaign\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class ManageTargetClientsDTO
{
    #[Assert\NotBlank(message: 'Client IDs are required')]
    #[Assert\Count(
        min: 1,
        minMessage: 'At least one client ID is required'
    )]
    #[Assert\All([
        new Assert\NotBlank(message: 'Client ID cannot be empty'),
        new Assert\Uuid(message: 'Each client ID must be a valid UUID')
    ])]
    /**
     * @var string[]
     */
    public array $clientIds = [];
}
