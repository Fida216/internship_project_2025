<?php

namespace App\Domain\Transaction\Repository;

use App\Domain\Transaction\Entity\Transaction;
use App\Shared\Enum\Currency;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class TransactionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Transaction::class);
    }

    /**
     * Find transactions by client ID
     */
    public function findByClientId(string $clientId): array
    {
        return $this->createQueryBuilder('t')
            ->where('t.client = :clientId')
            ->setParameter('clientId', $clientId)
            ->orderBy('t.transactionDate', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find transactions by exchange office ID
     */
    public function findByExchangeOfficeId(string $exchangeOfficeId): array
    {
        return $this->createQueryBuilder('t')
            ->where('t.exchangeOffice = :exchangeOfficeId')
            ->setParameter('exchangeOfficeId', $exchangeOfficeId)
            ->orderBy('t.transactionDate', 'DESC')
            ->getQuery()
            ->getResult();
    }

}
