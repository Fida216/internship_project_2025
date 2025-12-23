<?php

namespace App\Domain\MarketingCampaign\Mapper;

use App\Domain\MarketingCampaign\DTO\CreateMarketingCampaignDTO;
use App\Domain\MarketingCampaign\DTO\MarketingCampaignResponseDTO;
use App\Domain\MarketingCampaign\DTO\MarketingCampaignCreateResponseDTO;
use App\Domain\MarketingCampaign\Entity\MarketingCampaign;
use App\Domain\MarketingCampaign\Entity\CampaignTargetClient;
use App\Domain\ExchangeOffice\Entity\ExchangeOffice;
use App\Domain\User\Entity\UserInfo;
use App\Domain\Client\Entity\Client;
use App\Shared\Enum\CampaignStatus;

class MarketingCampaignMapper
{
    public static function fromCreateDTO(
        CreateMarketingCampaignDTO $dto,
        ExchangeOffice $exchangeOffice,
        UserInfo $user,
        array $clients
    ): MarketingCampaign {
        $campaign = new MarketingCampaign();
        $campaign->setTitle($dto->title);
        $campaign->setDescription($dto->description);
        $campaign->setStatus(CampaignStatus::from($dto->status));
        $campaign->setStartDate(new \DateTime($dto->startDate));
        $campaign->setEndDate(new \DateTime($dto->endDate));
        $campaign->setCreatedAt(new \DateTime());
        $campaign->setExchangeOffice($exchangeOffice);
        $campaign->setUser($user);

        return $campaign;
    }

    public static function toResponseDTO(MarketingCampaign $campaign, array $marketingActions = []): MarketingCampaignResponseDTO
    {
        return new MarketingCampaignResponseDTO($campaign, $marketingActions);
    }

    public static function toCreateResponseDTO(MarketingCampaign $campaign): MarketingCampaignCreateResponseDTO
    {
        return new MarketingCampaignCreateResponseDTO($campaign);
    }
}
