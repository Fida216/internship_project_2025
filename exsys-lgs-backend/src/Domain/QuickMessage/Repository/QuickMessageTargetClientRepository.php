<?php

namespace App\Domain\QuickMessage\Repository;

use App\Domain\QuickMessage\Entity\QuickMessageTargetClient;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<QuickMessageTargetClient>
 *
 * @method QuickMessageTargetClient|null find($id, $lockMode = null, $lockVersion = null)
 * @method QuickMessageTargetClient|null findOneBy(array $criteria, array $orderBy = null)
 * @method QuickMessageTargetClient[]    findAll()
 * @method QuickMessageTargetClient[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuickMessageTargetClientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, QuickMessageTargetClient::class);
    }

    public function save(QuickMessageTargetClient $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(QuickMessageTargetClient $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Find target clients by quick message
     */
    public function findByQuickMessage($quickMessageId): array
    {
        return $this->createQueryBuilder('qmtc')
            ->andWhere('qmtc.quickMessage = :quickMessageId')
            ->setParameter('quickMessageId', $quickMessageId)
            ->orderBy('qmtc.addedAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find target clients by client
     */
    public function findByClient($clientId): array
    {
        return $this->createQueryBuilder('qmtc')
            ->andWhere('qmtc.client = :clientId')
            ->setParameter('clientId', $clientId)
            ->orderBy('qmtc.addedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Check if client is already targeted by a quick message
     */
    public function isClientTargeted($quickMessageId, $clientId): bool
    {
        $result = $this->createQueryBuilder('qmtc')
            ->select('COUNT(qmtc.id)')
            ->andWhere('qmtc.quickMessage = :quickMessageId')
            ->andWhere('qmtc.client = :clientId')
            ->setParameter('quickMessageId', $quickMessageId)
            ->setParameter('clientId', $clientId)
            ->getQuery()
            ->getSingleScalarResult();

        return $result > 0;
    }
}
