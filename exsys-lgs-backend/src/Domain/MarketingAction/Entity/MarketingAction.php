<?php

namespace App\Domain\MarketingAction\Entity;

use App\Shared\Enum\ChannelType;
use App\Domain\User\Entity\UserInfo;
use App\Domain\MarketingCampaign\Entity\MarketingCampaign;
use App\Domain\MarketingCampaign\Entity\CampaignTargetClient;
use App\Domain\MarketingAction\Repository\MarketingActionRepository;
use Ramsey\Uuid\UuidInterface;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: MarketingActionRepository::class)]
#[ORM\Table(name: "marketingAction")]
class MarketingAction
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'Ramsey\\Uuid\\Doctrine\\UuidGenerator')]
    private UuidInterface $id;

    #[ORM\Column(type: "string")]
    private string $title;

    #[ORM\Column(type: "string", enumType: ChannelType::class)]
    private ChannelType $channelType;

    #[ORM\Column(type: "text")]
    private string $content;

    #[ORM\Column(type: "datetime", nullable: true)]
    private ?\DateTimeInterface $sentAt = null;

    #[ORM\ManyToOne(targetEntity: UserInfo::class)]
    #[ORM\JoinColumn(name: "user_id", referencedColumnName: "id", nullable: false)]
    private UserInfo $user;

    #[ORM\ManyToOne(targetEntity: MarketingCampaign::class)]
    #[ORM\JoinColumn(name: "campaign_id", referencedColumnName: "id", nullable: false)]
    private MarketingCampaign $campaign;

    #[ORM\Column(type: "datetime")]
    private \DateTimeInterface $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?UuidInterface
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getChannelType(): ChannelType
    {
        return $this->channelType;
    }

    public function setChannelType(ChannelType $channelType): self
    {
        $this->channelType = $channelType;
        return $this;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;
        return $this;
    }

    public function getSentAt(): ?\DateTimeInterface
    {
        return $this->sentAt;
    }

    public function setSentAt(?\DateTimeInterface $sentAt): self
    {
        $this->sentAt = $sentAt;
        return $this;
    }

    public function getUser(): UserInfo
    {
        return $this->user;
    }

    public function setUser(UserInfo $user): self
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return Collection<int, CampaignTargetClient>
     */
    public function getTargetClients(): Collection
    {
        return $this->campaign->getTargetClients();
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getCampaign(): MarketingCampaign
    {
        return $this->campaign;
    }

    public function setCampaign(MarketingCampaign $campaign): self
    {
        $this->campaign = $campaign;
        return $this;
    }
}
