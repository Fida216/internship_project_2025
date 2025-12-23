<?php

namespace App\Domain\Transaction\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class TransactionIDQueryDTO
{
    #[Assert\NotBlank(message: 'Transaction ID is required')]
    #[Assert\Uuid(message: 'Transaction ID must be a valid UUID')]
    public string $transactionId;
}
