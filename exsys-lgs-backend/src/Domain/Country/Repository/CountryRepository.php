<?php

namespace App\Domain\Country\Repository;

use App\Domain\Country\Entity\Country;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Country>
 */
class CountryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Country::class);
    }

    /**
     * Find all countries ordered by name
     */
    public function findAllActive(): array
    {
        return $this->createQueryBuilder('c')
            ->orderBy('c.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find country by code
     */
    public function findByCode(string $code): ?Country
    {
        return $this->createQueryBuilder('c')
            ->where('c.code = :code')
            ->setParameter('code', $code)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Find country by nationality
     */
    public function findByNationality(string $nationality): ?Country
    {
        return $this->createQueryBuilder('c')
            ->where('c.nationality = :nationality')
            ->setParameter('nationality', $nationality)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
