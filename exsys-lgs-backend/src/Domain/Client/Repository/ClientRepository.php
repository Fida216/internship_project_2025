<?php

namespace App\Domain\Client\Repository;

use App\Domain\Client\Entity\Client;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ClientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Client::class);
    }

    /**
     * Find a client by email
     */
    public function findOneByEmail(string $email): ?Client
    {
        return $this->createQueryBuilder('c')
            ->where('c.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Find clients by exchange office
     */
    public function findByExchangeOffice($exchangeOfficeId): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.exchangeOffice = :exchangeOfficeId')
            ->setParameter('exchangeOfficeId', $exchangeOfficeId, 'uuid')
            ->getQuery()
            ->getResult();
    }


    /**
     * Find a client by nationalId within the same exchange office
     */
    public function findByNationalIdInExchangeOffice(string $nationalId, $exchangeOfficeId): ?Client
    {
        $results = $this->createQueryBuilder('c')
            ->where('c.nationalId = :nationalId')
            ->andWhere('c.exchangeOffice = :exchangeOfficeId')
            ->setParameter('nationalId', $nationalId)
            ->setParameter('exchangeOfficeId', $exchangeOfficeId, 'uuid')
            ->getQuery()
            ->getResult();
        return $results[0] ?? null;
    }

    /**
     * Find a client by passport within the same exchange office
     */
    public function findByPassportInExchangeOffice(string $passport, $exchangeOfficeId): ?Client
    {
        $results = $this->createQueryBuilder('c')
            ->where('c.passport = :passport')
            ->andWhere('c.exchangeOffice = :exchangeOfficeId')
            ->setParameter('passport', $passport)
            ->setParameter('exchangeOfficeId', $exchangeOfficeId, 'uuid')
            ->getQuery()
            ->getResult();

        return $results[0] ?? null;
    }

    /**
     * Find clients by exchange office with optional filters and pagination
     */
    public function findByExchangeOfficeWithFilters(
        $exchangeOfficeId,
        ?string $status = null,
        ?string $search = null,
        int $limit = 20,
        int $offset = 0,
        ?string $nationality = null,
        ?string $gender = null,
        ?string $acquisitionSource = null,
        ?string $currentSegment = null
    ): array
    {
        $qb = $this->createQueryBuilder('c')
            ->where('c.exchangeOffice = :exchangeOfficeId')
            ->setParameter('exchangeOfficeId', $exchangeOfficeId, 'uuid')
            ->join('c.country', 'co')
            ->orderBy('c.createdAt', 'DESC');

        if ($status) {
            $qb->andWhere('c.status = :status')
               ->setParameter('status', $status);
        }

        if ($search) {
            $qb->andWhere(
                'c.firstName LIKE :search OR c.lastName LIKE :search OR c.email LIKE :search OR c.phone LIKE :search OR c.passport LIKE :search OR c.whatsapp LIKE :search OR c.nationalId LIKE :search OR c.residence LIKE :search'
            )
               ->setParameter('search', '%' . $search . '%');
        }

        if ($nationality) {
            $qb->andWhere('co.nationality = :nationality')
               ->setParameter('nationality', $nationality);
        }

        if ($gender) {
            $qb->andWhere('c.gender = :gender')
               ->setParameter('gender', $gender);
        }
        if ($acquisitionSource) {
            $qb->andWhere('c.acquisitionSource = :acquisitionSource')
               ->setParameter('acquisitionSource', $acquisitionSource);
        }
        if ($currentSegment) {
            $qb->andWhere('c.currentSegment LIKE :currentSegment')
               ->setParameter('currentSegment', '%' . $currentSegment . '%');
        }

        return $qb->setFirstResult($offset)
                  ->setMaxResults($limit)
                  ->getQuery()
                  ->getResult();
    }

    /**
     * Count clients by exchange office with optional filters
     */
    public function countByExchangeOfficeWithFilters(
        $exchangeOfficeId,
        ?string $status = null,
        ?string $search = null,
        ?string $nationality = null,
        ?string $gender = null,
        ?string $acquisitionSource = null,
        ?string $currentSegment = null
    ): int
    {
        $qb = $this->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->where('c.exchangeOffice = :exchangeOfficeId')
            ->setParameter('exchangeOfficeId', $exchangeOfficeId, 'uuid')
            ->join('c.country', 'co');

        if ($status) {
            $qb->andWhere('c.status = :status')
               ->setParameter('status', $status);
        }

        if ($search) {
            $qb->andWhere('c.firstName LIKE :search OR c.lastName LIKE :search')
               ->setParameter('search', '%' . $search . '%');
        }

        if ($nationality) {
            $qb->andWhere('co.nationality = :nationality')
               ->setParameter('nationality', $nationality);
        }

        if ($gender) {
            $qb->andWhere('c.gender = :gender')
               ->setParameter('gender', $gender);
        }
        if ($acquisitionSource) {
            $qb->andWhere('c.acquisitionSource = :acquisitionSource')
               ->setParameter('acquisitionSource', $acquisitionSource);
        }
        if ($currentSegment) {
            $qb->andWhere('c.currentSegment LIKE :currentSegment')
               ->setParameter('currentSegment', '%' . $currentSegment . '%');
        }

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * Find all clients with optional filters and pagination (Admin only)
     */
    public function findAllWithFilters(
        ?string $status = null,
        ?string $search = null,
        int $limit = 20,
        int $offset = 0,
        ?string $nationality = null,
        ?string $gender = null,
        ?string $exchangeOfficeId = null,
        ?string $acquisitionSource = null,
        ?string $currentSegment = null
    ): array
    {
        $qb = $this->createQueryBuilder('c')
            ->leftJoin('c.exchangeOffice', 'eo')
            ->addSelect('eo')
            ->join('c.country', 'co')
            ->orderBy('c.createdAt', 'DESC');

        if ($status) {
            $qb->andWhere('c.status = :status')
               ->setParameter('status', $status);
        }

        if ($search) {
            $qb->andWhere(
                'c.firstName LIKE :search OR c.lastName LIKE :search OR c.email LIKE :search OR c.phone LIKE :search OR c.passport LIKE :search OR c.whatsapp LIKE :search OR c.nationalId LIKE :search OR c.residence LIKE :search'
            )
               ->setParameter('search', '%' . $search . '%');
        }

        if ($nationality) {
            $qb->andWhere('co.nationality = :nationality')
               ->setParameter('nationality', $nationality);
        }

        if ($gender) {
            $qb->andWhere('c.gender = :gender')
               ->setParameter('gender', $gender);
        }

        if ($exchangeOfficeId) {
            $qb->andWhere('c.exchangeOffice = :exchangeOfficeId')
               ->setParameter('exchangeOfficeId', $exchangeOfficeId, 'uuid');
        }
        if ($acquisitionSource) {
            $qb->andWhere('c.acquisitionSource = :acquisitionSource')
               ->setParameter('acquisitionSource', $acquisitionSource);
        }
        if ($currentSegment) {
            $qb->andWhere('c.currentSegment LIKE :currentSegment')
               ->setParameter('currentSegment', '%' . $currentSegment . '%');
        }

        return $qb->setFirstResult($offset)
                  ->setMaxResults($limit)
                  ->getQuery()
                  ->getResult();
    }

    /**
     * Count all clients with optional filters (Admin only)
     */
    public function countAllWithFilters(
        ?string $status = null,
        ?string $search = null,
        ?string $nationality = null,
        ?string $gender = null,
        ?string $exchangeOfficeId = null,
        ?string $acquisitionSource = null,
        ?string $currentSegment = null
    ): int
    {
        $qb = $this->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->join('c.country', 'co');

        if ($status) {
            $qb->andWhere('c.status = :status')
               ->setParameter('status', $status);
        }

        if ($search) {
            $qb->andWhere(
                'c.firstName LIKE :search OR c.lastName LIKE :search OR c.email LIKE :search OR c.phone LIKE :search OR c.passport LIKE :search OR c.whatsapp LIKE :search OR c.nationalId LIKE :search OR c.residence LIKE :search'
            )
               ->setParameter('search', '%' . $search . '%');
        }

        if ($nationality) {
            $qb->andWhere('co.nationality = :nationality')
               ->setParameter('nationality', $nationality);
        }

        if ($gender) {
            $qb->andWhere('c.gender = :gender')
               ->setParameter('gender', $gender);
        }

        if ($exchangeOfficeId) {
            $qb->andWhere('c.exchangeOffice = :exchangeOfficeId')
               ->setParameter('exchangeOfficeId', $exchangeOfficeId, 'uuid');
        }
        if ($acquisitionSource) {
            $qb->andWhere('c.acquisitionSource = :acquisitionSource')
               ->setParameter('acquisitionSource', $acquisitionSource);
        }
        if ($currentSegment) {
            $qb->andWhere('c.currentSegment LIKE :currentSegment')
               ->setParameter('currentSegment', '%' . $currentSegment . '%');
        }

        return (int) $qb->getQuery()->getSingleScalarResult();
    }
}
