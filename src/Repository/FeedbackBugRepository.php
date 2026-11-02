<?php

namespace App\Repository;

use App\Entity\FeedbackBug;
use App\Entity\User;
use App\Repository\Interface\FeedbackInterface;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @extends ServiceEntityRepository<FeedbackBug>
 */
class FeedbackBugRepository extends ServiceEntityRepository implements FeedbackInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FeedbackBug::class);
    }
}
