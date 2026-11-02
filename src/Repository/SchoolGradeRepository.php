<?php

namespace App\Repository;

use App\Entity\School;
use App\Entity\SchoolGrade;
use App\Utils\UuidUtils;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\ArrayParameterType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Uid\Uuid;

/**
 * @extends ServiceEntityRepository<SchoolGrade>
 */
class SchoolGradeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SchoolGrade::class);
    }

    /**
     * @param string[] $uuids
     * @return SchoolGrade[]
     */
    public function findByUuidsForSchoolOrFail(int $schoolId, array $uuids): array
    {
        if ($uuids === []) {
            return [];
        }

        $uuidBinaries = array_map(
            static fn(string $uuid) => Uuid::fromString($uuid)->toBinary(),
            $uuids
        );


        $grades = $this->createQueryBuilder('g')
            ->andWhere('g.school = :schoolId')
            ->andWhere('g.uuid IN (:uuids)')
            ->setParameter('schoolId', $schoolId)
            ->setParameter('uuids', $uuidBinaries, ArrayParameterType::BINARY)
            ->getQuery()
            ->getResult();


        if (count($grades) !== count(array_unique($uuids))) {
            $foundUuids = array_map(
                static fn(SchoolGrade $g) => (string) $g->getUuid(),
                $grades,
            );
            $missing = array_diff($uuids, $foundUuids);

            throw new NotFoundHttpException(sprintf(
                'Grade(s) not found for this school: %s',
                implode(', ', $missing),
            ));
        }

        return $grades;
    }

    public function findBySchoolAndExternalId(School $schoolId, string $grade): SchoolGrade
    {
        return $this->findOneBy([
            'school' => $schoolId,
            'grade' => $grade
        ]);
    }
}
