<?php

namespace App\Domain\User\Repository;

use App\Domain\User\Entity\UserInfo;
use App\Domain\ExchangeOffice\Entity\ExchangeOffice;
use App\Shared\Enum\Role;
use App\Shared\Enum\Status;
use App\Domain\User\DTO\UserFilterDTO;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class UserInfoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserInfo::class);
    }

    public function findOneByEmail(string $email): ?UserInfo
    {
        return $this->createQueryBuilder('u')
            ->innerJoin('u.account', 'a')
            ->where('a.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findByExchangeOffice(ExchangeOffice $exchangeOffice): array
    {
        return $this->createQueryBuilder('u')
            ->where('u.exchangeOffice = :exchangeOfficeId')
            ->setParameter('exchangeOfficeId', $exchangeOffice->getId(), 'uuid')
            ->orderBy('u.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }


    public function findAgentsGroupedByExchangeOffice(): array
    {
        $agents = $this->createQueryBuilder('u')
            ->select('u, e')
            ->leftJoin('u.exchangeOffice', 'e')
            ->where('u.role = :role')
            ->setParameter('role', Role::AGENT->value)
            ->orderBy('e.name', 'ASC')
            ->addOrderBy('u.createdAt', 'DESC')
            ->getQuery()
            ->getResult();


        $grouped = [];
        foreach ($agents as $agent) {
            $exchangeOffice = $agent->getExchangeOffice();
            $exchangeOfficeName = $exchangeOffice ? $exchangeOffice->getName() : 'No Exchange Office';
            
            if (!isset($grouped[$exchangeOfficeName])) {
                $grouped[$exchangeOfficeName] = [];
            }
            
            $grouped[$exchangeOfficeName][] = $agent;
        }

        return $grouped;
    }

    public function findByFilters(UserFilterDTO $filterDto): array
    {
        $qb = $this->createQueryBuilder('u');
        
        if ($filterDto->exchangeOfficeId) {
            $qb->andWhere('u.exchangeOffice = :exchangeOfficeId')
               ->setParameter('exchangeOfficeId', $filterDto->exchangeOfficeId, 'uuid');
        }
        
        if ($filterDto->role) {
            $qb->andWhere('u.role = :role')
               ->setParameter('role', Role::from($filterDto->role)->value);
        }
        
        if ($filterDto->status) {
            $qb->andWhere('u.status = :status')
               ->setParameter('status', Status::from($filterDto->status)->value);
        }
        
        return $qb->orderBy('u.createdAt', 'DESC')
                  ->getQuery()
                  ->getResult();
    }
}
