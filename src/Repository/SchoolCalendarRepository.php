<?php

namespace App\Repository;

use App\Entity\SchoolCalendar;
use App\Entity\SchoolSchedule;
use DateTime;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

/**
 * @extends ServiceEntityRepository<SchoolCalendar>
 */
class SchoolCalendarRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SchoolCalendar::class);
    }

    public function findAllEntitiesByDateRange(int $schoolId, \DateTimeImmutable $from, \DateTimeImmutable $to): array
    {
        $rows = $this->createQueryBuilder('c')
            ->leftJoin('c.dayOff', 'd')
            ->addSelect('d')
            ->andWhere('c.school = :school')
            ->andWhere('c.date BETWEEN :from AND :to')
            ->setParameter('school', $schoolId)
            ->setParameter('from', $from, Types::DATE_IMMUTABLE)
            ->setParameter('to', $to, Types::DATE_IMMUTABLE)
            ->getQuery()
            ->getResult();

        $map = [];
        foreach ($rows as $row) {
            $map[$row->getDate()->format('Y-m-d')] = $row;
        }

        return $map;
    }

    public function findAllByDateRange(int $schoolId, DateTimeImmutable $from, DateTimeImmutable $to): array
    {
        $rows = $this->createQueryBuilder('c')
            ->select('c.date')
            ->where('c.school = :schoolId')
            ->andWhere('c.date >= :from')
            ->andWhere('c.date <= :to')
            ->setParameter('schoolId', $schoolId)
            ->setParameter('from', $from, Types::DATE_IMMUTABLE)
            ->setParameter('to', $to, Types::DATE_IMMUTABLE)
            ->getQuery()
            ->getArrayResult();

        $indexed = [];
        foreach ($rows as $row) {
            $indexed[$row['date']->format('Y-m-d')] = true;
        }

        return $indexed;
    }

    public function findLatestWithDayCycleOnOrBefore(int $schoolId, DateTimeImmutable $date): ?SchoolCalendar
    {
        return $this->createQueryBuilder('c')
            ->addSelect('dc')
            ->innerJoin('c.dayCycle', 'dc')
            ->where('c.school = :schoolId')
            ->andWhere('c.date <= :date')
            ->setParameter('schoolId', $schoolId)
            ->setParameter('date', $date, Types::DATE_IMMUTABLE)
            ->orderBy('c.date', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findOneBySchoolIdAndDate(int $schoolId, DateTimeImmutable $date): ?SchoolCalendar
    {
        return $this->createQueryBuilder('c')
            ->addSelect('s', 'd', 'dc', 'scope')
            ->leftJoin('c.schedule', 's')
            ->leftJoin('c.dayOff', 'd')
            ->leftJoin('d.scope', 'scope')
            ->leftJoin('c.dayCycle', 'dc')
            ->where('c.school = :schoolId')
            ->andWhere('c.date = :date')
            ->setParameter('schoolId', $schoolId)
            ->setParameter('date', $date, 'date_immutable')
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findOneBySchoolUuidAndDate(Uuid $schoolUuid, DateTimeImmutable $date): ?SchoolCalendar
    {
        return $this->createQueryBuilder("c")
            ->join('c.school', 'sc')
            ->where('sc.uuid = :schoolUuid')
            ->andWhere('c.date = :date')
            ->setParameter('schoolUuid', $schoolUuid, 'uuid')
            ->setParameter('date', $date, 'date_immutable')
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findOneBySchoolAndDate(int $schoolId, \DateTimeImmutable $date): ?SchoolCalendar
    {
        return $this->createQueryBuilder('c')
            ->where('c.school = :schoolId')
            ->andWhere('c.date = :date')
            ->setParameter('schoolId', $schoolId)
            ->setParameter('date', $date, 'date_immutable')
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function countByDayOff(int $dayOffId): int
    {
        return (int) $this->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->where('c.dayOff = :dayOffId')
            ->setParameter('dayOffId', $dayOffId)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @return \DateTimeImmutable[]
     */
    public function clearScheduleForAll(SchoolSchedule $schedule) : array
    {
        $em = $this->getEntityManager();

        $affectedDates = $em->createQuery(
            'SELECT sc.date FROM App\Entity\SchoolCalendar sc
             WHERE sc.schedule = :schedule'
        )
            ->setParameter('schedule', $schedule)
            ->getSingleColumnResult();

        $em->createQuery(
            'UPDATE App\Entity\SchoolCalendar sc
             SET sc.schedule = NULL
             WHERE sc.schedule = :schedule'
        )
            ->setParameter('schedule', $schedule)
            ->execute();


        return array_map(
            static fn(string $d) => new DateTimeImmutable($d),
            $affectedDates
        );
    }
}
