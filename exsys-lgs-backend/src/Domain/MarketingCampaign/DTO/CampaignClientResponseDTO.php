<?php

namespace App\Domain\MarketingCampaign\DTO;

use App\Domain\Client\Entity\Client;

class CampaignClientResponseDTO
{
    public string $id;
    public string $firstName;
    public string $lastName;

    public function __construct(Client $client)
    {
        $this->id = $client->getId()->toString();
        $this->firstName = $client->getFirstName();
        $this->lastName = $client->getLastName();
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
        ];
    }
}
