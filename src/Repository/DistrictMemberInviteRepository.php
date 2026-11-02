<?php

namespace App\Repository;

use App\Entity\DistrictMemberInvite;
use App\Repository\Interface\MemberInviteInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DistrictMemberInvite>
 */
class DistrictMemberInviteRepository extends ServiceEntityRepository implements MemberInviteInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DistrictMemberInvite::class);
    }

    public function getActiveInvites(): array
    {
        return $this->createQueryBuilder('i')
            ->leftJoin('i.district', 'd')
            ->addSelect('d')
            ->where('i.expiresAt > :now')
            ->andWhere('i.acceptedAt IS NULL')
            ->setParameter('now', new \DateTimeImmutable())
            ->getQuery()
            ->getResult();
    }
}
