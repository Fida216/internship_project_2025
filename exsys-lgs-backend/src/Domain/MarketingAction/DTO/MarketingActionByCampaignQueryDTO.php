<?php

namespace App\Domain\MarketingAction\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class MarketingActionByCampaignQueryDTO
{
    #[Assert\NotBlank(message: 'Campaign ID is required')]
    #[Assert\Uuid(message: 'Campaign ID must be a valid UUID')]
    public ?string $campaignId = null;
}
