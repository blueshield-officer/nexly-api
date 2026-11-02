<?php

namespace App\Repository;

use App\Entity\SchoolDayCycle;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

/**
 * @extends ServiceEntityRepository<SchoolDayCycle>
 */
class SchoolDayCycleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SchoolDayCycle::class);
    }

    public function findOneBySchoolIdAndUuid(
        int $schoolId,
        Uuid $uuid
    ) : ?SchoolDayCycle
    {
        return $this->createQueryBuilder('p')
            ->join('p.school', 'sc')
            ->where('p.uuid = :uuid')
            ->andWhere('sc.id = :schoolId')
            ->setParameter('uuid', $uuid, 'uuid')
            ->setParameter('schoolId', $schoolId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findOneBySchoolUuidAndDayCycleUuid(
        Uuid $schoolUuid,
        Uuid $dayCycleUuid
    ) : ?SchoolDayCycle
    {
        return $this->createQueryBuilder('p')
            ->join('p.school', 'sc')
            ->where('p.uuid = :dayCycleUuid')
            ->andWhere('sc.uuid = :schoolUuid')
            ->setParameter('dayCycleUuid', $dayCycleUuid, 'uuid')
            ->setParameter('schoolUuid', $schoolUuid, 'uuid')
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findAllBySchoolId(
        int $schoolId
    ): array
    {
        $result = $this->createQueryBuilder('d')
            ->join('d.school', 'sc')
            ->select(
                'd.uuid as uuid',
                'd.position',
                'd.code'
            )
            ->where('sc.id = :schoolId')
            ->setParameter('schoolId', $schoolId)
            ->getQuery()
            ->getResult();

        $indexed = [];
        foreach ($result as $row) {
            $uuid = $row['uuid'];
            unset($row['uuid']);
            $indexed[(string) $uuid] = $row;
        }
        return $indexed;
    }

    public function findHighestPositionBySchoolId(
        int $schoolId
    ): ?int
    {
        return $this->createQueryBuilder('d')
            ->select('MAX(d.position)')
            ->where('d.school = :schoolId')
            ->setParameter('schoolId', $schoolId)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function shiftPositions(int $schoolId, int $from, ?int $to, int $delta): void
    {
        $qb = $this->createQueryBuilder('c')
            ->update()
            ->set('c.position', 'c.position + :delta')
            ->where('c.school = :school')
            ->andWhere('c.position >= :from')
            ->setParameter('delta', $delta)
            ->setParameter('school', $schoolId)
            ->setParameter('from', $from);

        if ($to !== null) {
            $qb->andWhere('c.position <= :to')->setParameter('to', $to);
        }

        $qb->getQuery()->execute();
    }
}
