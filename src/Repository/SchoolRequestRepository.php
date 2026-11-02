<?php

namespace App\Repository;

use App\Entity\SchoolRequest;
use App\Entity\User;
use App\Enum\RequestStatusEnum;
use App\Repository\Interface\RequestInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SchoolRequest>
 */
class SchoolRequestRepository extends ServiceEntityRepository implements RequestInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SchoolRequest::class);
    }

    public function findInactive(User $user): array
    {
        return $this->createQueryBuilder('r')
            ->where("d.status NOT IN (:statuses)")
            ->andWhere("d.user = :user")
            ->setParameter("user", $user)
            ->setParameter("statuses", [RequestStatusEnum::Pending, RequestStatusEnum::InReview])
            ->getQuery()
            ->getResult();
    }

    public function findActive(User $user): ?SchoolRequest
    {
        return $this->createQueryBuilder("d")
            ->where("d.status IN (:statuses)")
            ->andWhere("d.user = :user")
            ->setParameter("user", $user)
            ->setParameter("statuses", [RequestStatusEnum::Pending, RequestStatusEnum::InReview])
            ->getQuery()
            ->getOneOrNullResult();
    }
}
