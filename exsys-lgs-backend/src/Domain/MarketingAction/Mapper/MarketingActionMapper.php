<?php

namespace App\Domain\MarketingAction\Mapper;
use App\Shared\Enum\ChannelType;
use Ramsey\Uuid\Uuid;

use App\Domain\MarketingAction\DTO\MarketingActionCreateDTO;
use App\Domain\MarketingAction\Entity\MarketingAction;
use App\Domain\MarketingCampaign\Entity\MarketingCampaign;

class MarketingActionMapper
{
   
    public static function fromCreateDTO(
        MarketingActionCreateDTO $dto,
        MarketingCampaign $campaign
    ): MarketingAction {
        $marketingAction = new MarketingAction();
        $marketingAction->setTitle($dto->title);
        $marketingAction->setChannelType(ChannelType::from($dto->channelType));
        $marketingAction->setContent($dto->content);
        $marketingAction->setCampaign($campaign);

        return $marketingAction;
    }
}
