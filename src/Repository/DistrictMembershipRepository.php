<?php

namespace App\Repository;

use App\Entity\DistrictMember;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DistrictMember>
 */
class DistrictMembershipRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DistrictMember::class);
    }

    public function findDistrictUuidByUser(User $userId) : ?string
    {
        $record = $this->findOneBy(['user' => $userId]);
        return $record?->getDistrict()->getUuid();
    }
}
