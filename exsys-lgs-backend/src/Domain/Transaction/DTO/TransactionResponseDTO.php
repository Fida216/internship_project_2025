<?php

namespace App\Domain\Transaction\DTO;

class TransactionResponseDTO
{
    private string $id;
    private string $amount;
    private string $sourceCurrency;
    private string $targetCurrency;
    private string $exchangeRate;
    private string $transactionDate;
    private ?ClientResponseDTO $client;
    private ?ExchangeOfficeResponseDTO $exchangeOffice;

    public function __construct(
        string $id,
        string $amount,
        string $sourceCurrency,
        string $targetCurrency,
        string $exchangeRate,
        string $transactionDate,
        ?ClientResponseDTO $client = null,
        ?ExchangeOfficeResponseDTO $exchangeOffice = null
    ) {
        $this->id = $id;
        $this->amount = $amount;
        $this->sourceCurrency = $sourceCurrency;
        $this->targetCurrency = $targetCurrency;
        $this->exchangeRate = $exchangeRate;
        $this->transactionDate = $transactionDate;
        $this->client = $client;
        $this->exchangeOffice = $exchangeOffice;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getAmount(): string
    {
        return $this->amount;
    }

    public function getSourceCurrency(): string
    {
        return $this->sourceCurrency;
    }

    public function getTargetCurrency(): string
    {
        return $this->targetCurrency;
    }

    public function getExchangeRate(): string
    {
        return $this->exchangeRate;
    }

    public function getTransactionDate(): string
    {
        return $this->transactionDate;
    }

    public function getClient(): ?ClientResponseDTO
    {
        return $this->client;
    }

    public function getExchangeOffice(): ?ExchangeOfficeResponseDTO
    {
        return $this->exchangeOffice;
    }

    public function toArray(): array
    {
        $data = [
            'id' => $this->id,
            'amount' => $this->amount,
            'sourceCurrency' => $this->sourceCurrency,
            'targetCurrency' => $this->targetCurrency,
            'exchangeRate' => $this->exchangeRate,
            'transactionDate' => $this->transactionDate,
        ];

        if ($this->client !== null) {
            $data['client'] = $this->client->toArray();
        }

        if ($this->exchangeOffice !== null) {
            $data['exchangeOffice'] = $this->exchangeOffice->toArray();
        }

        return $data;
    }
}
