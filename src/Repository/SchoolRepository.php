<?php

namespace App\Repository;

use App\Entity\School;
use App\Enum\SchoolIdentifierEnum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\AbstractQuery;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Uid\Uuid;

/**
 * @extends ServiceEntityRepository<School>
 */
class SchoolRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, School::class);
    }

    public function findOneByUuid(Uuid $uuid): ?School
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.uuid = :uuid')
            ->setParameter('uuid', $uuid, 'uuid')
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findOneByUuidOrFail(Uuid $uuid): School
    {
        $school = $this->findOneByUuid($uuid);
        if (!$school) {
            throw new NotFoundHttpException('School was not found');
        }
        return $school;
    }

    public function findIdPublicly(string $identifier, SchoolIdentifierEnum $identifierType): ?int
    {
        $query = $this->createQueryBuilder('s')
            ->select('s.id');

        if ($identifierType === SchoolIdentifierEnum::UUID) $query->andWhere('s.uuid = :identifier');
        else $query->andWhere('s.code = :identifier');

        return
            $query->setParameter(
                'identifier',
                $identifierType === SchoolIdentifierEnum::UUID ? Uuid::fromString($identifier) : $identifier,
                $identifierType === SchoolIdentifierEnum::UUID ? UuidType::NAME : null)
            ->getQuery()
            ->getOneOrNullResult(AbstractQuery::HYDRATE_SINGLE_SCALAR);
    }

    public function findById(int $id): ?array
    {
        $result = $this->createQueryBuilder('s')
            ->select('s')
            ->addSelect('colors', 'grades', 'sis')
            ->addSelect('IDENTITY(s.district) as districtId')
            ->leftJoin('s.colors', 'colors')
            ->leftJoin('s.grades', 'grades')
            ->leftJoin('s.sis', 'sis')
            ->where('s.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult(AbstractQuery::HYDRATE_ARRAY);

        if ($result === null) return null;

        $entity = $result[0];
        $entity['districtId'] = $result['districtId'];

        return $entity;
    }

    public function findSchoolsIdByDistrictId(int $districtId): array
    {
        return $this->createQueryBuilder('s')
            ->select('s.id')
            ->where('s.district = :districtId')
            ->setParameter('districtId', $districtId)
            ->getQuery()
            ->getResult(AbstractQuery::HYDRATE_SCALAR_COLUMN);
    }
}
