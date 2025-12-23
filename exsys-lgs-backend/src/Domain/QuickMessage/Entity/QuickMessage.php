<?php

namespace App\Domain\QuickMessage\Entity;

use App\Shared\Enum\ChannelType;
use App\Domain\User\Entity\UserInfo;
use App\Domain\ExchangeOffice\Entity\ExchangeOffice;
use App\Domain\QuickMessage\Repository\QuickMessageRepository;
use Ramsey\Uuid\UuidInterface;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: QuickMessageRepository::class)]
#[ORM\Table(name: "quickMessage")]
class QuickMessage
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

    #[ORM\OneToMany(mappedBy: "quickMessage", targetEntity: QuickMessageTargetClient::class, cascade: ["persist", "remove"])]
    private Collection $targetClients;

    #[ORM\ManyToOne(targetEntity: ExchangeOffice::class)]
    #[ORM\JoinColumn(name: "exchange_office_id", referencedColumnName: "id", nullable: false)]
    private ExchangeOffice $exchangeOffice;

    #[ORM\Column(type: "datetime")]
    private \DateTimeInterface $createdAt;

    public function __construct()
    {
        $this->targetClients = new ArrayCollection();
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
     * @return Collection<int, QuickMessageTargetClient>
     */
    public function getTargetClients(): Collection
    {
        return $this->targetClients;
    }

    public function addTargetClient(QuickMessageTargetClient $targetClient): self
    {
        if (!$this->targetClients->contains($targetClient)) {
            $this->targetClients->add($targetClient);
            $targetClient->setQuickMessage($this);
        }

        return $this;
    }

    public function removeTargetClient(QuickMessageTargetClient $targetClient): self
    {
        if ($this->targetClients->removeElement($targetClient)) {
            // set the owning side to null (unless already changed)
            if ($targetClient->getQuickMessage() === $this) {
                $targetClient->setQuickMessage(null);
            }
        }

        return $this;
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

    public function getExchangeOffice(): ExchangeOffice
    {
        return $this->exchangeOffice;
    }

    public function setExchangeOffice(ExchangeOffice $exchangeOffice): self
    {
        $this->exchangeOffice = $exchangeOffice;
        return $this;
    }
}
