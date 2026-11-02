<?php

namespace App\Repository;

use App\Entity\DistrictRequest;
use App\Entity\User;
use App\Enum\RequestStatusEnum;
use App\Repository\Interface\RequestInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Google\Service\DataManager\Resource\RequestStatus;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

/**
 * @extends ServiceEntityRepository<DistrictRequest>
 */
class DistrictRequestRepository extends ServiceEntityRepository implements RequestInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DistrictRequest::class);
    }

    public function findInactive(User $user): array
    {
        return $this->createQueryBuilder('r')
            ->where('r.user = :user')
            ->andWhere('r.status NOT IN (:statuses)')
            ->setParameter('statuses', [RequestStatusEnum::Pending, RequestStatusEnum::InReview])
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }

    public function findActive(User $user): ?DistrictRequest
    {
        return $this->createQueryBuilder("d")
            ->where("d.status IN (:statuses)")
            ->andWhere("d.user = :user")
            ->setParameter("statuses", [RequestStatusEnum::Pending, RequestStatusEnum::InReview])
            ->setParameter("user", $user)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
