<?php

namespace App\Repository;

use App\Entity\ExternalSat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ExternalSat>
 */
class ExternalSatRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ExternalSat::class);
    }

    public function findAll(): array
    {
        $future = $this->createQueryBuilder('s')
            ->select('s.testDate', 's.registrationDeadline', 's.lateRegistrationDeadline')
            ->where('s.testDate > :currentDate')
            ->setParameter('currentDate', new \DateTimeImmutable())
            ->getQuery()
            ->getResult();

        $lastPast = $this->createQueryBuilder('s')
            ->select('s.testDate', 's.registrationDeadline', 's.lateRegistrationDeadline')
            ->where('s.testDate <= :currentDate')
            ->setParameter('currentDate', new \DateTimeImmutable())
            ->orderBy('s.testDate', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        $result = $lastPast ? array_merge([$lastPast], $future) : $future;

        foreach ($result as &$row) {
            $row['testDate'] = $row['testDate']->format('Y-m-d');
            $row['registrationDeadline'] = $row['registrationDeadline']?->format('Y-m-d');
            $row['lateRegistrationDeadline'] = $row['lateRegistrationDeadline']?->format('Y-m-d');
        }

        return $result;

    }

    public function persist(ExternalSat $entity): void
    {
        $this->getEntityManager()->persist($entity);
    }

    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }

}
