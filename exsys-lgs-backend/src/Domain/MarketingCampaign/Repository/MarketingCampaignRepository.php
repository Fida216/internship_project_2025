<?php

namespace App\Domain\MarketingCampaign\Repository;

use App\Domain\MarketingCampaign\Entity\MarketingCampaign;
use App\Shared\Enum\CampaignStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MarketingCampaign>
 *
 * @method MarketingCampaign|null find($id, $lockMode = null, $lockVersion = null)
 * @method MarketingCampaign|null findOneBy(array $criteria, array $orderBy = null)
 * @method MarketingCampaign[]    findAll()
 * @method MarketingCampaign[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MarketingCampaignRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MarketingCampaign::class);
    }

    public function save(MarketingCampaign $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(MarketingCampaign $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Find campaigns by exchange office
     */
    public function findByExchangeOffice($exchangeOfficeId): array
    {
        return $this->createQueryBuilder('mc')
            ->innerJoin('mc.exchangeOffice', 'eo')
            ->where('eo.id = :exchangeOfficeId')
            ->setParameter('exchangeOfficeId', $exchangeOfficeId, 'uuid')
            ->orderBy('mc.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

}
