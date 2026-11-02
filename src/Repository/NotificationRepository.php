<?php

namespace App\Repository;

use App\Entity\DistrictNotification;
use App\Entity\Notification;
use App\Entity\SchoolNotification;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Notification>
 */
class NotificationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Notification::class);
    }

    public function findFeedBySchoolIdAndDistrictId(
        int $schoolId,
        int | null $districtId,
        ?\DateTimeImmutable $before = null,
        int $limit = 50,
    ): array
    {
        $qb = $this->createQueryBuilder('n')
            ->leftJoin(SchoolNotification::class, 'sn', 'WITH', 'sn.id = n.id')
            ->leftJoin(DistrictNotification::class, 'dn', 'WITH', 'dn.id = n.id');

        $or = $qb->expr()->orX(
            'sn.id IS NULL AND dn.id IS NULL',   // website
            'IDENTITY(sn.school) = :schoolId',   // this school's
        );

        $qb->setParameter('schoolId', $schoolId);

        if ($districtId !== null) {
            $or->add('IDENTITY(dn.district) = :districtId');
            $qb->setParameter('districtId', $districtId);
        }

        $qb->andWhere($or)
            ->orderBy('n.publishedAt', 'DESC')
            ->setMaxResults(min($limit, 50));

        if ($before) {
            $qb->andWhere('n.publishedAt < :before')->setParameter('before', $before);
        }

        return $qb->getQuery()->getResult();
    }

    //    /**
    //     * @return Notification[] Returns an array of Notification objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('n')
    //            ->andWhere('n.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('n.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Notification
    //    {
    //        return $this->createQueryBuilder('n')
    //            ->andWhere('n.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
