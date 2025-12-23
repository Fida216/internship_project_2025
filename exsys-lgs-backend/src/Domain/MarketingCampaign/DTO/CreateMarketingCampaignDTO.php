<?php

namespace App\Domain\MarketingCampaign\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use App\Shared\Enum\CampaignStatus;

class CreateMarketingCampaignDTO
{
    #[Assert\NotBlank(message: "Title is required")]
    #[Assert\Length(max: 255, maxMessage: "Title cannot exceed 255 characters")]
    public string $title;

    #[Assert\NotBlank(message: "Description is required")]
    public string $description;

    #[Assert\NotBlank(message: "Status is required")]
    #[Assert\Choice(callback: [CampaignStatus::class, 'getAllValues'], message: "Status must be one of: draft, active, completed, cancelled")]
    public string $status;

    #[Assert\NotBlank(message: "Start date is required")]
    #[Assert\Date(message: "Start date must be a valid date")]
    public string $startDate;

    #[Assert\NotBlank(message: "End date is required")]
    #[Assert\Date(message: "End date must be a valid date")]
    public string $endDate;

    /**
     * @var CampaignTargetClientDTO[]
     */
    #[Assert\NotEmpty(message: "At least one target client is required")]
    #[Assert\All([
        new Assert\Type(type: CampaignTargetClientDTO::class, message: "Each target must be a valid CampaignTargetClientDTO")
    ])]
    public array $targets = [];
}
