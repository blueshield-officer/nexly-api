<?php

namespace App\Repository;

use App\Entity\School;
use App\Entity\SchoolCalendarEvent;
use App\Enum\SchoolCalendarEventTypeEnum;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @extends ServiceEntityRepository<SchoolCalendarEvent>
 */
class SchoolCalendarEventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SchoolCalendarEvent::class);
    }

    public function findStructureBySchoolId(int $schoolId, ?\DateTimeImmutable $date = null): array
    {
        $date ??= new \DateTimeImmutable('today');

        $yearEnd = $this->createQueryBuilder('e')
            ->where('e.school = :schoolId')
            ->andWhere('e.type = :type')
            ->andWhere('e.dateStart >= :yearStartDate')
            ->setParameter('schoolId', $schoolId)
            ->setParameter('type', SchoolCalendarEventTypeEnum::YEAR_END)
            ->setParameter('yearStartDate', $date, Types::DATE_IMMUTABLE)
            ->orderBy('e.dateStart', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        $yearStart = null;
        if ($yearEnd !== null) {
            $yearStart = $this->createQueryBuilder('e')
                ->where('e.school = :schoolId')
                ->andWhere('e.type = :type')
                ->andWhere('e.dateStart <= :yearEndDate')
                ->setParameter('schoolId', $schoolId)
                ->setParameter('type', SchoolCalendarEventTypeEnum::YEAR_START)
                ->setParameter('yearEndDate', $yearEnd->getDateStart(), Types::DATE_IMMUTABLE)
                ->orderBy('e.dateStart', 'DESC')
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();
        }


        $graduation = $this->createQueryBuilder('e')
            ->where('e.school = :schoolId')
            ->andWhere('e.type = :type')
            ->andWhere('e.dateStart >= :date')
            ->setParameter('schoolId', $schoolId)
            ->setParameter('type', SchoolCalendarEventTypeEnum::GRADUATION)
            ->setParameter('date', $date, Types::DATE_IMMUTABLE)
            ->orderBy('e.dateStart', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        return [
            'yearStart'  => $yearStart?->getDateStart(),
            'yearEnd'    => $yearEnd?->getDateStart(),
            'graduation' => $graduation?->getDateStart(),
        ];
    }

    public function hasSchoolYearConfigured(int $schoolId): bool
    {
        $count = $this->createQueryBuilder('e')
            ->select('COUNT(e.id)')
            ->where('e.school = :schoolId')
            ->andWhere('e.type IN (:types)')
            ->setParameter('schoolId', $schoolId)
            ->setParameter('types', [
                SchoolCalendarEventTypeEnum::YEAR_START,
                SchoolCalendarEventTypeEnum::YEAR_END,
            ])
            ->getQuery()
            ->getSingleScalarResult();

        return $count >= 2;
    }

    public function getSchoolYearStartDate(int $schoolId, \DateTimeImmutable $date): ?\DateTimeImmutable
    {
        $event = $this->createQueryBuilder('e')
            ->where('e.school = :schoolId')
            ->andWhere('e.type = :type')
            ->andWhere('e.dateStart <= :date')
            ->setParameter('schoolId', $schoolId)
            ->setParameter('type', SchoolCalendarEventTypeEnum::YEAR_START->value)
            ->setParameter('date', $date, Types::DATE_IMMUTABLE)
            ->orderBy('e.dateStart', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        return $event?->getDateStart();
    }

    public function getSchoolYearEndDate(int $schoolId, \DateTimeImmutable $date): ?\DateTimeImmutable
    {
        $event = $this->createQueryBuilder('e')
            ->where('e.school = :schoolId')
            ->andWhere('e.type = :type')
            ->setParameter('schoolId', $schoolId)
            ->setParameter('type', SchoolCalendarEventTypeEnum::YEAR_END->value)
            ->setParameter('date', $date, Types::DATE_IMMUTABLE)
            ->andWhere('e.dateStart >= :date')
            ->orderBy('e.dateStart', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        return $event?->getDateStart();
    }

    public function isWithinSchoolYear(int $schoolId, \DateTimeImmutable $date): bool
    {
        $yearStart = $this->createQueryBuilder('e')
            ->where('e.school = :schoolId')
            ->andWhere('e.type = :type')
            ->andWhere('e.dateStart <= :date')
            ->setParameter('schoolId', $schoolId)
            ->setParameter('type', SchoolCalendarEventTypeEnum::YEAR_START)
            ->setParameter('date', $date, Types::DATE_IMMUTABLE)
            ->orderBy('e.dateStart', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        if ($yearStart === null) {
            return false;
        }

        $yearEnd = $this->createQueryBuilder('e')
            ->where('e.school = :schoolId')
            ->andWhere('e.type = :type')
            ->andWhere('e.dateStart >= :yearStartDate')
            ->setParameter('schoolId', $schoolId)
            ->setParameter('type', SchoolCalendarEventTypeEnum::YEAR_END)
            ->setParameter('yearStartDate', $yearStart->getDateStart(), 'date_immutable')
            ->orderBy('e.dateStart', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        if ($yearEnd === null) {
            return false;
        }

        return $date >= $yearStart->getDateStart() && $date <= $yearEnd->getDateStart();
    }

    public function getSchoolCalendarEventsAfterTheDate(int $schoolId, DateTimeImmutable $date): array
    {
        $rows = $this->createQueryBuilder('e')
            ->select('e.dateStart AS dateStart', 'e.dateEnd AS dateEnd', 'e.type AS type')
            ->andWhere('e.school = :schoolId')
            ->andWhere('e.dateStart >= :date')
            ->setParameter('schoolId', $schoolId)
            ->setParameter('date', $date)
            ->orderBy('e.dateStart', 'ASC')
            ->getQuery()
            ->getArrayResult();

        $grouped = [];
        foreach ($rows as $row) {
            $ymdStart = $row['dateStart']->format('Y-m-d');

            $grouped[$ymdStart][] = [
                'name' => $row['type']->value,
                'ymdStart' => $ymdStart,
                'ymdEnd' => $row['dateEnd']?->format('Y-m-d'),
            ];
        }

        return $grouped;
    }
}
