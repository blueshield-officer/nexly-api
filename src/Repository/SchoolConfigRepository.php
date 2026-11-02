<?php

namespace App\Repository;

use App\Entity\SchoolConfig;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SchoolConfig>
 */
class SchoolConfigRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SchoolConfig::class);
    }

    public function findOneBySchoolId(int $schoolId): ?array
    {
        return $this->createQueryBuilder('config')
            ->select(
                'defaultSchedule.uuid as defaultScheduleUuid',
                'config.earlyDismissalThresholdMinutes'
            )
            ->leftJoin('config.defaultSchedule', 'defaultSchedule')
            ->where('config.school = :school')
            ->setParameter('school', $schoolId)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
