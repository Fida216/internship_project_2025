<?php

namespace App\Domain\Transaction\DTO;

class TransactionCreateResponseDTO
{
    private string $message;
    private TransactionResponseDTO $transaction;

    public function __construct(
        string $message,
        TransactionResponseDTO $transaction
    ) {
        $this->message = $message;
        $this->transaction = $transaction;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getTransaction(): TransactionResponseDTO
    {
        return $this->transaction;
    }

    public function toArray(): array
    {
        return [
            'message' => $this->message,
            'transaction' => $this->transaction->toArray(),
        ];
    }
}
