<?php

namespace App\Domain\MarketingCampaign\Repository;

use App\Domain\MarketingCampaign\Entity\CampaignTargetClient;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CampaignTargetClient>
 *
 * @method CampaignTargetClient|null find($id, $lockMode = null, $lockVersion = null)
 * @method CampaignTargetClient|null findOneBy(array $criteria, array $orderBy = null)
 * @method CampaignTargetClient[]    findAll()
 * @method CampaignTargetClient[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CampaignTargetClientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CampaignTargetClient::class);
    }

    public function save(CampaignTargetClient $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(CampaignTargetClient $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
