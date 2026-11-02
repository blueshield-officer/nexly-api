<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\UserRefreshToken;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserRefreshToken>
 */
class UserRefreshTokenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserRefreshToken::class);
    }

    public function findOneByUserAndToken(User $user, string $hashedToken): ?UserRefreshToken
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.user = :user')
            ->andWhere('t.token = :token')
            ->setParameter('user', $user)
            ->setParameter('token', $hashedToken)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findOneByToken(string $token): ?UserRefreshToken
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.token = :token')
            ->setParameter('token', $token)
            ->getQuery()
            ->getOneOrNullResult();
    }

    //    /**
    //     * @return UserRefreshToken[] Returns an array of UserRefreshToken objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('u.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?UserRefreshToken
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
