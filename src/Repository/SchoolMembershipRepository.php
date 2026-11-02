<?php

namespace App\Repository;

use App\Entity\DistrictMember;
use App\Entity\SchoolMember;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SchoolMember>
 */
class SchoolMembershipRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SchoolMember::class);
    }

    public function existsForUserAndSchool(int $userId, int $schoolId): bool
    {
        return $this->createQueryBuilder('s')
            ->where('s.user = :userId')
            ->andWhere('s.school = :schoolId')
            ->setParameter('userId', $userId)
            ->setParameter('schoolId', $schoolId)
            ->getQuery()
            ->getOneOrNullResult() !== null;
    }

    public function findSchoolUuidByUser(User $userId): ?string
    {
        $record = $this->findOneBy(['user' => $userId]);
        return $record?->getSchool()->getUuid();
    }
}
