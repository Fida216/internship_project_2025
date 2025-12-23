<?php

namespace App\Domain\Transaction\Entity;

use Ramsey\Uuid\UuidInterface;
use Doctrine\ORM\Mapping as ORM;
use App\Domain\Transaction\Repository\TransactionRepository;
use App\Domain\Client\Entity\Client;
use App\Domain\ExchangeOffice\Entity\ExchangeOffice;
use App\Shared\Enum\Currency;

#[ORM\Entity(repositoryClass: TransactionRepository::class)]
#[ORM\Table(name: "transactionEX")]
class Transaction
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'Ramsey\\Uuid\\Doctrine\\UuidGenerator')]
    private UuidInterface $id;

    #[ORM\Column(type: "decimal", precision: 15, scale: 2)]
    private $amount;

    #[ORM\Column(type: "string", enumType: Currency::class)]
    private Currency $sourceCurrency;

    #[ORM\Column(type: "string", enumType: Currency::class)]
    private Currency $targetCurrency;

    #[ORM\Column(type: "decimal", precision: 10, scale: 6)]
    private $exchangeRate;

    #[ORM\Column(type: "datetime")]
    private \DateTimeInterface $transactionDate;

    #[ORM\ManyToOne(targetEntity: Client::class)]
    #[ORM\JoinColumn(name: "client_id", referencedColumnName: "id")]
    private $client;

    #[ORM\ManyToOne(targetEntity: ExchangeOffice::class)]
    #[ORM\JoinColumn(name: "exchange_office_id", referencedColumnName: "id")]
    private $exchangeOffice;

    public function getId(): ?UuidInterface
    {
        return $this->id;
    }

    public function getAmount(): ?string
    {
        return $this->amount;
    }

    public function setAmount(string $amount): self
    {
        $this->amount = $amount;
        return $this;
    }

    public function getSourceCurrency(): ?Currency
    {
        return $this->sourceCurrency;
    }

    public function setSourceCurrency(Currency $sourceCurrency): self
    {
        $this->sourceCurrency = $sourceCurrency;
        return $this;
    }

    public function getTargetCurrency(): ?Currency
    {
        return $this->targetCurrency;
    }

    public function setTargetCurrency(Currency $targetCurrency): self
    {
        $this->targetCurrency = $targetCurrency;
        return $this;
    }

    public function getExchangeRate(): ?string
    {
        return $this->exchangeRate;
    }

    public function setExchangeRate(string $exchangeRate): self
    {
        $this->exchangeRate = $exchangeRate;
        return $this;
    }

    public function getTransactionDate(): ?\DateTimeInterface
    {
        return $this->transactionDate;
    }

    public function setTransactionDate(\DateTimeInterface $transactionDate): self
    {
        $this->transactionDate = $transactionDate;
        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): self
    {
        $this->client = $client;
        return $this;
    }

    public function getExchangeOffice(): ?ExchangeOffice
    {
        return $this->exchangeOffice;
    }

    public function setExchangeOffice(?ExchangeOffice $exchangeOffice): self
    {
        $this->exchangeOffice = $exchangeOffice;
        return $this;
    }
}
