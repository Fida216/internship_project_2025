<?php

namespace App\Domain\MarketingCampaign\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use App\Domain\ExchangeOffice\Entity\ExchangeOffice;
use App\Domain\User\Entity\UserInfo;
use App\Domain\MarketingCampaign\Repository\MarketingCampaignRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Shared\Enum\CampaignStatus;

#[ORM\Entity(repositoryClass: MarketingCampaignRepository::class)]
#[ORM\Table(name: "marketingCampaign")]
class MarketingCampaign
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'Ramsey\\Uuid\\Doctrine\\UuidGenerator')]
    private UuidInterface $id;

    #[ORM\Column(type: 'string')]
    private $title;

    #[ORM\Column(type: 'string')]
    private $description;

    #[ORM\Column(type: 'string', enumType: CampaignStatus::class)]
    private CampaignStatus $status;

    #[ORM\Column(type: 'date')]
    private $startDate;

    #[ORM\Column(type: 'date')]
    private $endDate;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $createdAt;

    #[ORM\ManyToOne(targetEntity: ExchangeOffice::class)]
    #[ORM\JoinColumn(name: 'exchange_office_id', referencedColumnName: 'id', nullable: false)]
    private ExchangeOffice $exchangeOffice;

    #[ORM\ManyToOne(targetEntity: UserInfo::class)]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false)]
    private UserInfo $user;

    #[ORM\OneToMany(mappedBy: 'marketingCampaign', targetEntity: CampaignTargetClient::class, cascade: ['persist', 'remove'])]
    private Collection $targetClients;

    public function __construct()
    {
        $this->targetClients = new ArrayCollection();
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?UuidInterface { return $this->id; }
    public function getTitle(): ?string { return $this->title; }
    public function setTitle(string $title): self { $this->title = $title; return $this; }
    public function getDescription(): ?string { return $this->description; }
    public function setDescription(string $description): self { $this->description = $description; return $this; }
    public function getStatus(): ?CampaignStatus { return $this->status; }
    public function setStatus(CampaignStatus $status): self { $this->status = $status; return $this; }
    public function getStartDate(): ?\DateTimeInterface { return $this->startDate; }
    public function setStartDate(\DateTimeInterface $startDate): self { $this->startDate = $startDate; return $this; }
    public function getEndDate(): ?\DateTimeInterface { return $this->endDate; }
    public function setEndDate(\DateTimeInterface $endDate): self { $this->endDate = $endDate; return $this; }
    public function getCreatedAt(): ?\DateTimeInterface { return $this->createdAt; }
    public function setCreatedAt(\DateTimeInterface $createdAt): self { $this->createdAt = $createdAt; return $this; }
    public function getExchangeOffice(): ?ExchangeOffice { return $this->exchangeOffice; }
    public function setExchangeOffice(ExchangeOffice $exchangeOffice): self { $this->exchangeOffice = $exchangeOffice; return $this; }
    public function getUser(): ?UserInfo { return $this->user; }
    public function setUser(UserInfo $user): self { $this->user = $user; return $this; }

    /**
     * @return Collection<int, CampaignTargetClient>
     */
    public function getTargetClients(): Collection { return $this->targetClients; }
    
    public function addTargetClient(CampaignTargetClient $targetClient): self 
    { 
        if (!$this->targetClients->contains($targetClient)) {
            $this->targetClients->add($targetClient);
            $targetClient->setMarketingCampaign($this);
        }
        return $this; 
    }
    
    public function removeTargetClient(CampaignTargetClient $targetClient): self 
    { 
        if ($this->targetClients->removeElement($targetClient)) {
            if ($targetClient->getMarketingCampaign() === $this) {
                $targetClient->setMarketingCampaign(null);
            }
        }
        return $this; 
    }
}
