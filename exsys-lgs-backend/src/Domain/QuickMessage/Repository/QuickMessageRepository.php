<?php

namespace App\Domain\QuickMessage\Repository;

use App\Domain\QuickMessage\Entity\QuickMessage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<QuickMessage>
 *
 * @method QuickMessage|null find($id, $lockMode = null, $lockVersion = null)
 * @method QuickMessage|null findOneBy(array $criteria, array $orderBy = null)
 * @method QuickMessage[]    findAll()
 * @method QuickMessage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuickMessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, QuickMessage::class);
    }

    public function save(QuickMessage $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(QuickMessage $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Find messages by user
     */
    public function findByUser($userId): array
    {
        return $this->createQueryBuilder('qm')
            ->andWhere('qm.user = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('qm.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find messages by status
     */
    public function findByStatus($status): array
    {
        return $this->createQueryBuilder('qm')
            ->andWhere('qm.status = :status')
            ->setParameter('status', $status)
            ->orderBy('qm.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find messages by channel type
     */
    public function findByChannelType($channelType): array
    {
        return $this->createQueryBuilder('qm')
            ->andWhere('qm.channelType = :channelType')
            ->setParameter('channelType', $channelType)
            ->orderBy('qm.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
