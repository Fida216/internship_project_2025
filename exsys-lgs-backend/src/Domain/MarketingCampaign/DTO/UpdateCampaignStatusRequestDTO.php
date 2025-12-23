<?php

namespace App\Domain\MarketingCampaign\DTO;

use App\Shared\Enum\CampaignStatus;
use Symfony\Component\Validator\Constraints as Assert;

class UpdateCampaignStatusRequestDTO
{
    #[Assert\NotNull(message: "Status is required")]
    public CampaignStatus $status;
}
