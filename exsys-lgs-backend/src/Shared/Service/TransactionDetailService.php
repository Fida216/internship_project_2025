<?php

namespace App\Shared\Service;

use App\Shared\Repository\TransactionDetailRepository;
use App\Shared\DTO\TransactionDetailDTO;

class TransactionDetailService
{
    public function __construct(
        private TransactionDetailRepository $transactionDetailRepository
    ) {}

    /**
     * Get all transactions with complete details
     * 
     * @return TransactionDetailDTO[]
     */
    public function getAllTransactionDetails(): array
    {
        return $this->transactionDetailRepository->findAllTransactionDetails();
    }
}
