<?php

namespace App\Repository;

use App\Entity\FeedbackIdea;
use App\Entity\User;
use App\Repository\Interface\FeedbackInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @extends ServiceEntityRepository<FeedbackIdea>
 */
class FeedbackIdeaRepository extends ServiceEntityRepository implements FeedbackInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FeedbackIdea::class);
    }

    public function getLatestDate(UserInterface $user): ?\DateTimeImmutable
    {
        $result = $this->createQueryBuilder('p')
            ->select('p.createdAt')
            ->where('p.user = :user')
            ->setParameter('user', $user)
            ->orderBy('p.createdAt', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        return $result['createdAt'] ?? null;
    }
}
