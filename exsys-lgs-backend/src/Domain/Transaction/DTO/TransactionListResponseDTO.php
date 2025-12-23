<?php

namespace App\Domain\Transaction\DTO;

class TransactionListResponseDTO
{
    private string $message;
    /** @var TransactionResponseDTO[] */
    private array $transactions;
    private ?ExchangeOfficeResponseDTO $exchangeOffice;

    /**
     * @param TransactionResponseDTO[] $transactions
     */
    public function __construct(
        string $message,
        array $transactions,
        ?ExchangeOfficeResponseDTO $exchangeOffice = null
    ) {
        $this->message = $message;
        $this->transactions = $transactions;
        $this->exchangeOffice = $exchangeOffice;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @return TransactionResponseDTO[]
     */
    public function getTransactions(): array
    {
        return $this->transactions;
    }

    public function getExchangeOffice(): ?ExchangeOfficeResponseDTO
    {
        return $this->exchangeOffice;
    }

    public function toArray(): array
    {
        $data = [
            'message' => $this->message,
            'transactions' => array_map(fn($transaction) => $transaction->toArray(), $this->transactions),
        ];

        if ($this->exchangeOffice !== null) {
            $data['exchangeOffice'] = $this->exchangeOffice->toArray();
        }

        return $data;
    }
}
