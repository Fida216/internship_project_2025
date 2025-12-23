<?php

namespace App\Domain\ExchangeOffice\Repository;

use App\Domain\ExchangeOffice\Entity\ExchangeOffice;
use App\Shared\Enum\Status;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ExchangeOfficeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ExchangeOffice::class);
    }

 
    public function findOneByEmail(string $email): ?ExchangeOffice
    {
        return $this->findOneBy(['email' => $email]);
    }


    public function findAllOffices(): array
    {
        return $this->createQueryBuilder('eo')
            ->orderBy('eo.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
