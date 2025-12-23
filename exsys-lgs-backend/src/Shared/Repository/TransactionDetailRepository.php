<?php

namespace App\Shared\Repository;

use App\Shared\DTO\TransactionDetailDTO;
use App\Domain\Transaction\Entity\Transaction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\Expr\Join;

class TransactionDetailRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Transaction::class);
    }

    /**
     * Get all transactions with complete details using optimized SQL query
     * 
     * @return TransactionDetailDTO[]
     */
    public function findAllTransactionDetails(): array
    {
        $qb = $this->createQueryBuilder('t')
            ->select([
                't.id as transactionId',
                't.amount',
                't.sourceCurrency',
                't.targetCurrency', 
                't.transactionDate',
                'c.id as clientId',
                'c.firstName',
                'c.lastName',
                'c.phone as clientPhone',
                'c.email as clientEmail',
                'eo.id as bureauId',
                'eo.name as bureauName',
                'eo.address as bureauAddress',
                'eo.phone as bureauPhone',
                'country.name as countryName'
            ])
            ->leftJoin('t.client', 'c')
            ->leftJoin('t.exchangeOffice', 'eo')
            ->leftJoin('c.country', 'country')
            ->orderBy('t.transactionDate', 'DESC');

        $results = $qb->getQuery()->getArrayResult();

        return array_map(function ($row) {
            return new TransactionDetailDTO(
                transactionId: $row['transactionId']->toString(),
                clientId: $row['clientId'] ? $row['clientId']->toString() : '',
                bureauId: $row['bureauId'] ? $row['bureauId']->toString() : '',
                currencyFrom: $row['sourceCurrency']->value,
                currencyTo: $row['targetCurrency']->value,
                amount: $row['amount'],
                date: $row['transactionDate']->format('Y-m-d H:i:s'),
                firstName: $row['firstName'] ?? '',
                lastName: $row['lastName'] ?? '',
                clientPhone: $row['clientPhone'] ?? '',
                clientEmail: $row['clientEmail'] ?? '',
                clientAddress: $row['countryName'] ?? '',
                bureauName: $row['bureauName'] ?? '',
                bureauAddress: $row['bureauAddress'] ?? '',
                bureauPhone: $row['bureauPhone'] ?? ''
            );
        }, $results);
    }
}
