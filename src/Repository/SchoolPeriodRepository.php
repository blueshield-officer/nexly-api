<?php

namespace App\Repository;

use App\Entity\SchoolPeriod;
use App\Entity\SchoolSchedule;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Uid\Uuid;

/**
 * @extends ServiceEntityRepository<SchoolPeriod>
 */
class SchoolPeriodRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SchoolPeriod::class);
    }

    public function findAllBySchoolId(int $schoolId): array
    {
        $result = $this->createQueryBuilder('p')
            ->select('p.uuid', 'p.name')
            ->where('p.school = :school')
            ->setParameter('school', $schoolId)
            ->orderBy('p.name', 'ASC')
            ->getQuery()
            ->getArrayResult();

        $indexed = [];
        foreach ($result as $row) {
            $uuid = $row['uuid'];
            unset($row['uuid']);
            $indexed[(string) $uuid] = $row;
        }
        return $indexed;
    }

    public function findOneBySchoolUuidAndPeriodUuid(
        Uuid $schoolUuid,
        Uuid $periodUuid,
    ): ?SchoolPeriod
    {
        return $this->createQueryBuilder('p')
            ->join('p.school', 'sc')
            ->where('p.uuid = :periodUuid')
            ->andWhere('sc.uuid = :schoolUuid')
            ->setParameter('periodUuid', $periodUuid, 'uuid')
            ->setParameter('schoolUuid', $schoolUuid, 'uuid')
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findOneBySchoolIdAndPeriodUuid(
        int $schoolId,
        Uuid $periodUuid,
    ): ?SchoolPeriod
    {
        return $this->createQueryBuilder('p')
            ->join('p.school', 'sc')
            ->where('p.uuid = :periodUuid')
            ->andWhere('sc.id = :schoolId')
            ->setParameter('periodUuid', $periodUuid, 'uuid')
            ->setParameter('schoolId', $schoolId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findOneBySchoolIdAndPeriodUuidOrFail(
        int $schoolId,
        Uuid $periodUuid,
    ): SchoolPeriod
    {
        $schoolPeriod = $this->findOneBySchoolIdAndPeriodUuid($schoolId, $periodUuid);

        if ($schoolPeriod === null) {
            throw new NotFoundHttpException(sprintf(
                'SchoolPeriod with uuid "%s" not found for school id "%d".',
                $periodUuid,
                $schoolId,
            ));
        }

        return $schoolPeriod;
    }
}
