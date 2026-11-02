<?php

namespace App\Repository;

use App\Entity\SchoolSchedule;
use App\Entity\SchoolSchedulePeriod;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Uid\Uuid;

/**
 * @extends ServiceEntityRepository<SchoolSchedulePeriod>
 */
class SchoolSchedulePeriodRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SchoolSchedulePeriod::class);
    }

    public function findOneBySchoolUuidScheduleUuidAndPeriodUuidOrFail(
        Uuid $schoolUuid,
        Uuid $scheduleUuid,
        Uuid $schedulePeriodUuid
    ): SchoolSchedulePeriod
    {
        $result = $this->createQueryBuilder('ssp')
            ->innerJoin('ssp.schedule', 'sch')
            ->innerJoin('sch.school', 's')
            ->andWhere('s.uuid = :schoolUuid')
            ->andWhere('sch.uuid = :scheduleUuid')
            ->andWhere('ssp.uuid = :schedulePeriodUuid')
            ->setParameter('schoolUuid', $schoolUuid, 'uuid')
            ->setParameter('scheduleUuid', $scheduleUuid, 'uuid')
            ->setParameter('schedulePeriodUuid', $schedulePeriodUuid, 'uuid')
            ->getQuery()
            ->getOneOrNullResult();

        if (!$result instanceof SchoolSchedulePeriod) {
            throw new NotFoundHttpException('School schedule period not found.');
        }

        return $result;
    }
    public function deleteAllBySchedule(SchoolSchedule $schedule): int
    {
        return $this->getEntityManager()
            ->createQuery(
                'DELETE FROM App\Entity\SchoolSchedulePeriod ssp
             WHERE ssp.schedule = :schedule'
            )
            ->setParameter('schedule', $schedule)
            ->execute();
    }
}
