<?php

namespace App\Repository;

use App\Entity\SchoolMemberInvite;
use App\Repository\Interface\MemberInviteInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SchoolMemberInvite>
 */
class SchoolMemberInviteRepository extends ServiceEntityRepository implements MemberInviteInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SchoolMemberInvite::class);
    }

    public function getActiveInvites(): array
    {
        return $this->createQueryBuilder('i')
            ->leftJoin('i.school', 'd')
            ->addSelect('d')
            ->where('i.expiresAt > :now')
            ->andWhere('i.acceptedAt IS NULL')
            ->setParameter('now', new \DateTimeImmutable())
            ->getQuery()
            ->getResult();
    }
}
