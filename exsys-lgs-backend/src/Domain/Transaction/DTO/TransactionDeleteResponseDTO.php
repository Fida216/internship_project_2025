<?php

namespace App\Domain\Transaction\DTO;

class TransactionDeleteResponseDTO
{
    private string $message;
    private string $transactionId;

    public function __construct(
        string $message,
        string $transactionId
    ) {
        $this->message = $message;
        $this->transactionId = $transactionId;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getTransactionId(): string
    {
        return $this->transactionId;
    }

    public function toArray(): array
    {
        return [
            'message' => $this->message,
            'transactionId' => $this->transactionId,
        ];
    }
}
