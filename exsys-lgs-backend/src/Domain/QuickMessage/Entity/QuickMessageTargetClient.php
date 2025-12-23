<?php

namespace App\Domain\QuickMessage\Entity;

use App\Domain\Client\Entity\Client;
use App\Domain\QuickMessage\Repository\QuickMessageTargetClientRepository;
use Ramsey\Uuid\UuidInterface;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: QuickMessageTargetClientRepository::class)]
#[ORM\Table(name: "quickMessageTargetClient")]
class QuickMessageTargetClient
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'Ramsey\\Uuid\\Doctrine\\UuidGenerator')]
    private UuidInterface $id;

    #[ORM\ManyToOne(targetEntity: QuickMessage::class, inversedBy: "targetClients")]
    #[ORM\JoinColumn(name: "quickMessage_id", referencedColumnName: "id", nullable: false)]
    private ?QuickMessage $quickMessage = null;

    #[ORM\ManyToOne(targetEntity: Client::class)]
    #[ORM\JoinColumn(name: "client_id", referencedColumnName: "id", nullable: false)]
    private Client $client;

    #[ORM\Column(type: "datetime")]
    private \DateTimeInterface $addedAt;

    public function __construct()
    {
        $this->addedAt = new \DateTime();
    }

    public function getId(): ?UuidInterface
    {
        return $this->id;
    }

    public function getQuickMessage(): ?QuickMessage
    {
        return $this->quickMessage;
    }

    public function setQuickMessage(?QuickMessage $quickMessage): self
    {
        $this->quickMessage = $quickMessage;
        return $this;
    }

    public function getClient(): Client
    {
        return $this->client;
    }

    public function setClient(Client $client): self
    {
        $this->client = $client;
        return $this;
    }

    public function getAddedAt(): \DateTimeInterface
    {
        return $this->addedAt;
    }

    public function setAddedAt(\DateTimeInterface $addedAt): self
    {
        $this->addedAt = $addedAt;
        return $this;
    }
}
