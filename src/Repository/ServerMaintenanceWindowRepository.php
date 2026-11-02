<?php

namespace App\Repository;

use App\Entity\ServerMaintenanceWindow;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ServerMaintenanceWindow>
 */
class ServerMaintenanceWindowRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ServerMaintenanceWindow::class);
    }

    public function findActive(): ?ServerMaintenanceWindow
    {
        $now = new DateTimeImmutable();
        $today = $now->format('Y-m-d');


        return $this->createQueryBuilder('m')
            ->where('(m.startsAt <= :now AND m.endsAt >= :now)')
            ->orWhere('(SUBSTRING(m.startsAt, 1, 10) = :today AND m.endsAt > :now)')
            ->setParameter('now', $now)
            ->setParameter('today', $today)
            ->orderBy('m.startsAt', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
