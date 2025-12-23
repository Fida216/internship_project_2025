<?php

namespace App\Domain\Transaction\Service;

use App\Domain\Transaction\Entity\Transaction;
use App\Domain\Transaction\DTO\CreateTransactionDTO;
use App\Domain\Transaction\DTO\UpdateTransactionDTO;
use App\Domain\Transaction\DTO\TransactionCreateResponseDTO;
use App\Domain\Transaction\DTO\TransactionDeleteResponseDTO;
use App\Domain\Transaction\DTO\TransactionListResponseDTO;
use App\Domain\Transaction\Repository\TransactionRepository;
use App\Domain\Transaction\Mapper\TransactionResponseMapper;
use App\Domain\Client\Repository\ClientRepository;
use App\Domain\ExchangeOffice\Repository\ExchangeOfficeRepository;
use App\Domain\User\Repository\UserInfoRepository;
use App\Domain\User\Entity\UserInfo;
use App\Shared\Enum\Role;
use Doctrine\ORM\EntityManagerInterface;

class TransactionService
{
    private EntityManagerInterface $entityManager;
    private TransactionRepository $transactionRepository;
    private ClientRepository $clientRepository;
    private ExchangeOfficeRepository $exchangeOfficeRepository;
    private UserInfoRepository $userInfoRepository;
    private TransactionResponseMapper $responseMapper;

    public function __construct(
        EntityManagerInterface $entityManager,
        TransactionRepository $transactionRepository,
        ClientRepository $clientRepository,
        ExchangeOfficeRepository $exchangeOfficeRepository,
        UserInfoRepository $userInfoRepository,
        TransactionResponseMapper $responseMapper
    ) {
        $this->entityManager = $entityManager;
        $this->transactionRepository = $transactionRepository;
        $this->clientRepository = $clientRepository;
        $this->exchangeOfficeRepository = $exchangeOfficeRepository;
        $this->userInfoRepository = $userInfoRepository;
        $this->responseMapper = $responseMapper;
    }

    /**
     * Create a transaction for an agent's client
     */
    public function createTransactionFromDto(CreateTransactionDTO $dto, UserInfo $currentUser): TransactionCreateResponseDTO
    {
        
        // Get the agent's exchange office
        $agentExchangeOffice = $currentUser->getExchangeOffice();
        if (!$agentExchangeOffice) {
            throw new \InvalidArgumentException('Agent must be assigned to an exchange office');
        }

        // Verify the client exists and belongs to the agent's exchange office
        $client = $this->clientRepository->find($dto->clientId);
        if (!$client) {
            throw new \InvalidArgumentException('Client not found');
        }

        if ($client->getExchangeOffice()->getId()->toString() !== $agentExchangeOffice->getId()->toString()) {
            throw new \InvalidArgumentException('You can only create transactions for clients of your exchange office');
        }

        // Verify source and target currencies are different
        if ($dto->sourceCurrency === $dto->targetCurrency) {
            throw new \InvalidArgumentException('Source and target currencies must be different');
        }

        // Create transaction entity
        $transaction = new Transaction();
        $transaction->setAmount($dto->amount);
        $transaction->setSourceCurrency($dto->sourceCurrency);
        $transaction->setTargetCurrency($dto->targetCurrency);
        $transaction->setExchangeRate($dto->exchangeRate);
        $transaction->setTransactionDate($dto->transactionDate);
        $transaction->setClient($client);
        $transaction->setExchangeOffice($agentExchangeOffice);

        // Persist the transaction
        $this->entityManager->persist($transaction);
        $this->entityManager->flush();

        // Return transaction data using response mapper
        return $this->responseMapper->mapTransactionToCreateResponseDTO($transaction);
    }

    /**
     * Get transactions for agent's exchange office
     */
    public function getTransactionsForAgent(UserInfo $agent): TransactionListResponseDTO
    {
        $agentExchangeOffice = $agent->getExchangeOffice();
        if (!$agentExchangeOffice) {
            throw new \InvalidArgumentException('Agent must be assigned to an exchange office');
        }

        $transactions = $this->transactionRepository->findByExchangeOfficeId(
            $agentExchangeOffice->getId()->toString()
        );

        return $this->responseMapper->mapTransactionsToListResponseDTO(
            $transactions,
            'Transactions retrieved successfully'
        );
    }

    /**
     * Get transactions for a specific exchange office (Admin only)
     */
    public function getTransactionsByExchangeOffice(string $exchangeOfficeId): TransactionListResponseDTO
    {
        
        // Verify the exchange office exists
        $exchangeOffice = $this->exchangeOfficeRepository->find($exchangeOfficeId);
        if (!$exchangeOffice) {
            throw new \InvalidArgumentException('Exchange office not found');
        }

        $transactions = $this->transactionRepository->findByExchangeOfficeId($exchangeOfficeId);

        return $this->responseMapper->mapTransactionsToListResponseDTO(
            $transactions,
            'Transactions retrieved successfully',
            $exchangeOffice
        );
    }

    /**
     * Get transactions for a specific client (Agent and Admin access)
     * - Agents: can only view transactions for clients of their exchange office
     * - Admins: can view transactions for any client
     */
    public function getTransactionsByClient(UserInfo $user, string $clientId): TransactionListResponseDTO
    {

        // Verify the client exists
        $client = $this->clientRepository->find($clientId);
        if (!$client) {
            throw new \InvalidArgumentException('Client not found');
        }

        // For agents: verify the client belongs to their exchange office
        if ($user->getRole() === Role::AGENT) {
            $agentExchangeOffice = $user->getExchangeOffice();
            if (!$agentExchangeOffice) {
                throw new \InvalidArgumentException('Agent must be assigned to an exchange office');
            }

            if ($client->getExchangeOffice()->getId()->toString() !== $agentExchangeOffice->getId()->toString()) {
                throw new \InvalidArgumentException('You can only view transactions for clients of your exchange office');
            }
        }
        // For admins: no restrictions, they can view any client's transactions

        $transactions = $this->transactionRepository->findByClientId($clientId);

        return $this->responseMapper->mapTransactionsToListResponseDTO(
            $transactions,
            'Client transactions retrieved successfully'
        );
    }

    /**
     * Update a transaction (Admin only)
     */
    public function updateTransaction(string $transactionId, UpdateTransactionDTO $updateTransactionDTO): TransactionCreateResponseDTO
    {

        // Find the transaction
        $transaction = $this->transactionRepository->find($transactionId);
        if (!$transaction) {
            throw new \InvalidArgumentException('Transaction not found');
        }

    
        // Verify source and target currencies are different
        if ($updateTransactionDTO->sourceCurrency === $updateTransactionDTO->targetCurrency) {
            throw new \InvalidArgumentException('Source and target currencies must be different');
        }

        // Update transaction entity
        $transaction->setAmount($updateTransactionDTO->amount);
        $transaction->setSourceCurrency($updateTransactionDTO->sourceCurrency);
        $transaction->setTargetCurrency($updateTransactionDTO->targetCurrency);
        $transaction->setExchangeRate($updateTransactionDTO->exchangeRate);
        $transaction->setTransactionDate($updateTransactionDTO->transactionDate);

        // Persist the changes
        $this->entityManager->flush();

        // Return updated transaction data using response mapper
        return $this->responseMapper->mapTransactionToCreateResponseDTO($transaction);
    }

    /**
     * Delete a transaction (Admin only)
     */
    public function deleteTransaction(string $transactionId): TransactionDeleteResponseDTO
    {
        // Find the transaction
        $transaction = $this->transactionRepository->find($transactionId);
        if (!$transaction) {
            throw new \InvalidArgumentException('Transaction not found');
        }

        // Remove the transaction
        $this->entityManager->remove($transaction);
        $this->entityManager->flush();

        return $this->responseMapper->mapTransactionToDeleteResponseDTO($transactionId);
    }
}
