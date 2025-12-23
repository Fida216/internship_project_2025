<?php

namespace App\Domain\QuickMessage\Mapper;
use App\Shared\Enum\ChannelType;
use Ramsey\Uuid\Uuid;

use App\Domain\QuickMessage\DTO\QuickMessageCreateDTO;
use App\Domain\QuickMessage\Entity\QuickMessage;
use App\Domain\QuickMessage\Entity\QuickMessageTargetClient;
use App\Domain\Client\Entity\Client;
use App\Domain\ExchangeOffice\Entity\ExchangeOffice;

class QuickMessageMapper
{
    public static function fromCreateDTO(
        QuickMessageCreateDTO $dto,
        ExchangeOffice $exchangeOffice,
        array $clients
    ): QuickMessage {
        $quickMessage = new QuickMessage();
        $quickMessage->setTitle($dto->title);
        $quickMessage->setChannelType(ChannelType::from($dto->channelType));
        $quickMessage->setContent($dto->content);
        $quickMessage->setExchangeOffice($exchangeOffice);

        foreach ($clients as $client) {
            $target = new QuickMessageTargetClient();
            $target->setClient($client);
            $quickMessage->addTargetClient($target);
        }
        return $quickMessage;
    }
}
