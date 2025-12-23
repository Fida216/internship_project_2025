<?php

namespace App\Domain\Recommendation\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use App\Domain\Client\Entity\Client;
use App\Domain\Recommendation\Repository\RecommendationRepository;

#[ORM\Entity(repositoryClass: RecommendationRepository::class)]
#[ORM\Table(name: "recommendation")]
class Recommendation
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'Ramsey\\Uuid\\Doctrine\\UuidGenerator')]
    private UuidInterface $id;

    #[ORM\Column(type: 'string')]
    private $recommendationType;

    #[ORM\Column(type: 'string')]
    private $description;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $generatedAt;

    #[ORM\ManyToOne(targetEntity: Client::class)]
    #[ORM\JoinColumn(name: 'client_id', referencedColumnName: 'id', nullable: false)]
    private Client $client;

    #[ORM\Column(type: 'string')]
    private $status;

    public function getId(): ?UuidInterface { return $this->id; }
    public function getRecommendationType(): ?string { return $this->recommendationType; }
    public function setRecommendationType(string $recommendationType): self { $this->recommendationType = $recommendationType; return $this; }
    public function getDescription(): ?string { return $this->description; }
    public function setDescription(string $description): self { $this->description = $description; return $this; }
    public function getGeneratedAt(): ?\DateTimeInterface { return $this->generatedAt; }
    public function setGeneratedAt(\DateTimeInterface $generatedAt): self { $this->generatedAt = $generatedAt; return $this; }
    public function getClient(): ?Client { return $this->client; }
    public function setClient(Client $client): self { $this->client = $client; return $this; }
    public function getStatus(): ?string { return $this->status; }
    public function setStatus(string $status): self { $this->status = $status; return $this; }
}
