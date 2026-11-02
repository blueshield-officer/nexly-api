<?php

namespace App\Repository;

use App\Entity\School;
use App\Entity\SchoolSchedule;
use App\Exception\NotFound\SchoolScheduleNotFoundException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Uid\Uuid;

/**
 * @extends ServiceEntityRepository<SchoolSchedule>
 */
class SchoolScheduleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SchoolSchedule::class);
    }

    public function findAllBySchoolId(int $schoolId): array
    {
        $results = $this->createQueryBuilder('s')
            ->select('s', 'p')
            ->leftJoin('s.schoolSchedulePeriods', 'p')
            ->where('s.school = :school')
            ->setParameter('school', $schoolId)
            ->addOrderBy('s.code', 'ASC')
            ->addOrderBy('p.startTime', 'ASC')
            ->getQuery()
            ->getResult();

        $indexed = [];

        foreach ($results as $schedule) {
            $uuid = (string) $schedule->getUuid();
            $code = $schedule->getCode();
            $description = $schedule->getDescription();
            $isHidden = $schedule->isHidden();

            $indexed[$uuid] = [
                'code' => $code,
                'description' => $description,
                'isHidden' => $isHidden,
                'periods' => [],
            ];

            $periods = $schedule->getSchoolSchedulePeriods()->toArray();

            foreach ($periods as $period) {
                $indexed[$uuid]['periods'][] = [
                    'uuid' => (string) $period->getUuid(),
                    'periodUuid' => (string) $period->getPeriod()->getUuid(),
                    'startTime' => $period->getStartTime()->format('H:i'),
                    'endTime' => $period->getEndTime()?->format('H:i'),
                ];
            }
        }

        return $indexed;
    }

    public function findOneBySchoolUuidAndScheduleUuid(
        Uuid $schoolUuid,
        Uuid $scheduleUuid,
    ): ?SchoolSchedule
    {
        return $this->createQueryBuilder('s')
            ->join('s.school', 'sc')
            ->where('s.uuid = :scheduleUuid')
            ->andWhere('sc.uuid = :schoolUuid')
            ->setParameter('scheduleUuid', $scheduleUuid, 'uuid')
            ->setParameter('schoolUuid', $schoolUuid, 'uuid')
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findOneBySchoolUuidAndScheduleUuidOrFail(
        Uuid $schoolUuid,
        Uuid $scheduleUuid,
    ): SchoolSchedule
    {
        $result = $this->findOneBySchoolUuidAndScheduleUuid($schoolUuid, $scheduleUuid);

        if (!$result) {
            throw new SchoolScheduleNotFoundException();
        }

        return $result;
    }

    public function findOneBySchoolIdAndScheduleUuidOrFail(int $schoolId, Uuid $uuid): SchoolSchedule
    {
        $schedule = $this->createQueryBuilder('s')
            ->andWhere('s.uuid = :uuid')
            ->andWhere('s.school = :schoolId')
            ->setParameter('uuid', $uuid, 'uuid')
            ->setParameter('schoolId', $schoolId)
            ->getQuery()
            ->getOneOrNullResult();

        if ($schedule === null) {
            throw new SchoolScheduleNotFoundException();
        }

        return $schedule;
    }

    public function findBySchoolAndExternalId(School $school, string $externalId): ?SchoolSchedule
    {
        return $this->findOneBy([
            'externalId' => $externalId,
            'school'     => $school,
        ]);
    }
}
