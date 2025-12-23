<?php

namespace App\Domain\Transaction\Mapper;

use App\Domain\Transaction\Entity\Transaction;
use App\Domain\Transaction\DTO\TransactionResponseDTO;
use App\Domain\Transaction\DTO\ClientResponseDTO;
use App\Domain\Transaction\DTO\ExchangeOfficeResponseDTO;
use App\Domain\Transaction\DTO\TransactionCreateResponseDTO;
use App\Domain\Transaction\DTO\TransactionDeleteResponseDTO;
use App\Domain\Transaction\DTO\TransactionListResponseDTO;
use App\Domain\Client\Entity\Client;
use App\Domain\ExchangeOffice\Entity\ExchangeOffice;

class TransactionResponseMapper
{
    public function mapTransactionToResponseDTO(Transaction $transaction): TransactionResponseDTO
    {
        $clientDTO = null;
        if ($transaction->getClient()) {
            $clientDTO = $this->mapClientToResponseDTO($transaction->getClient());
        }

        $exchangeOfficeDTO = null;
        if ($transaction->getExchangeOffice()) {
            $exchangeOfficeDTO = $this->mapExchangeOfficeToResponseDTO($transaction->getExchangeOffice());
        }

        return new TransactionResponseDTO(
            $transaction->getId(),
            $transaction->getAmount(),
            $transaction->getSourceCurrency()->value,
            $transaction->getTargetCurrency()->value,
            $transaction->getExchangeRate(),
            $transaction->getTransactionDate()->format('Y-m-d H:i:s'),
            $clientDTO,
            $exchangeOfficeDTO
        );
    }

    public function mapClientToResponseDTO(Client $client): ClientResponseDTO
    {
        return new ClientResponseDTO(
            $client->getId(),
            $client->getFirstName(),
            $client->getLastName(),
            $client->getEmail()
        );
    }

    public function mapExchangeOfficeToResponseDTO(ExchangeOffice $exchangeOffice): ExchangeOfficeResponseDTO
    {
        return new ExchangeOfficeResponseDTO(
            $exchangeOffice->getId(),
            $exchangeOffice->getName()
        );
    }

    /**
     * @param Transaction[] $transactions
     */
    public function mapTransactionsToListResponseDTO(
        array $transactions, 
        string $message, 
        ?ExchangeOffice $exchangeOffice = null
    ): TransactionListResponseDTO {
        $transactionDTOs = array_map(
            fn(Transaction $transaction) => $this->mapTransactionToResponseDTO($transaction),
            $transactions
        );

        $exchangeOfficeDTO = null;
        if ($exchangeOffice) {
            $exchangeOfficeDTO = $this->mapExchangeOfficeToResponseDTO($exchangeOffice);
        }

        return new TransactionListResponseDTO(
            $message,
            $transactionDTOs,
            $exchangeOfficeDTO
        );
    }

    public function mapTransactionToCreateResponseDTO(Transaction $transaction): TransactionCreateResponseDTO
    {
        $transactionDTO = $this->mapTransactionToResponseDTO($transaction);
        
        return new TransactionCreateResponseDTO(
            'Transaction created successfully',
            $transactionDTO
        );
    }

    public function mapTransactionToDeleteResponseDTO(string $transactionId): TransactionDeleteResponseDTO
    {
        return new TransactionDeleteResponseDTO(
            'Transaction deleted successfully',
            $transactionId
        );
    }
}
