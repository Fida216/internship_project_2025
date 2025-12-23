<?php

namespace App\Domain\MarketingCampaign\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use App\Domain\MarketingCampaign\Entity\MarketingCampaign;
use App\Domain\Client\Entity\Client;
use App\Domain\MarketingCampaign\Repository\CampaignTargetClientRepository;

#[ORM\Entity(repositoryClass: CampaignTargetClientRepository::class)]
#[ORM\Table(name: "campaignTargetClient")]
class CampaignTargetClient
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'Ramsey\\Uuid\\Doctrine\\UuidGenerator')]
    private UuidInterface $id;

    #[ORM\ManyToOne(targetEntity: MarketingCampaign::class, inversedBy: 'targetClients')]
    #[ORM\JoinColumn(name: 'marketing_campaign_id', referencedColumnName: 'id', nullable: false)]
    private MarketingCampaign $marketingCampaign;

    #[ORM\ManyToOne(targetEntity: Client::class)]
    #[ORM\JoinColumn(name: 'client_id', referencedColumnName: 'id', nullable: false)]
    private Client $client;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $addedAt;

    public function getId(): ?UuidInterface { return $this->id; }
    public function getMarketingCampaign(): ?MarketingCampaign { return $this->marketingCampaign; }
    public function setMarketingCampaign(MarketingCampaign $marketingCampaign): self { $this->marketingCampaign = $marketingCampaign; return $this; }
    public function getClient(): ?Client { return $this->client; }
    public function setClient(Client $client): self { $this->client = $client; return $this; }
    public function getAddedAt(): ?\DateTimeInterface { return $this->addedAt; }
    public function setAddedAt(\DateTimeInterface $addedAt): self { $this->addedAt = $addedAt; return $this; }
}
