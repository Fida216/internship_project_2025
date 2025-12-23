<?php

namespace App\Domain\ClientSegmentHistory\Repository;

use App\Domain\ClientSegmentHistory\Entity\ClientSegmentHistory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ClientSegmentHistoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ClientSegmentHistory::class);
    }

    /**
     * Find segment history entries by client ID
     *
     * @param string $clientId
     * @return ClientSegmentHistory[]
     */
    public function findByClientId(string $clientId): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.client = :clientId')
            ->setParameter('clientId', $clientId)
            ->orderBy('c.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
