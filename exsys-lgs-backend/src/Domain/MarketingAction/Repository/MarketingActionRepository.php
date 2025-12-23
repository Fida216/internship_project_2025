<?php

namespace App\Domain\MarketingAction\Repository;

use App\Domain\MarketingAction\Entity\MarketingAction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MarketingAction>
 *
 * @method MarketingAction|null find($id, $lockMode = null, $lockVersion = null)
 * @method MarketingAction|null findOneBy(array $criteria, array $orderBy = null)
 * @method MarketingAction[]    findAll()
 * @method MarketingAction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MarketingActionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MarketingAction::class);
    }

    public function save(MarketingAction $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(MarketingAction $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Find actions by user
     */
    public function findByUser($userId): array
    {
        return $this->createQueryBuilder('ma')
            ->andWhere('ma.user = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('ma.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find actions by campaign
     */
    public function findByCampaign($campaignId): array
    {
        return $this->createQueryBuilder('ma')
            ->andWhere('ma.campaign = :campaignId')
            ->setParameter('campaignId', $campaignId)
            ->orderBy('ma.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find actions by channel type
     */
    public function findByChannelType($channelType): array
    {
        return $this->createQueryBuilder('ma')
            ->andWhere('ma.channelType = :channelType')
            ->setParameter('channelType', $channelType)
            ->orderBy('ma.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
