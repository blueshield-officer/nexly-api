<?php

namespace App\Repository;

use App\Entity\ServerStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ServerStatus>
 */
class ServerStatusRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ServerStatus::class);
    }

    public function getActiveServerStatuses(): ServerStatus|null
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.isActive = :isActive')
            ->setParameter('isActive', true)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
