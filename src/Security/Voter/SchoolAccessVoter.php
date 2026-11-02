<?php

namespace App\Security\Voter;

use App\Dto\SchoolDto;
use App\Entity\School;
use App\Entity\User;
use App\Repository\SchoolMembershipRepository;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

final class SchoolAccessVoter extends Voter
{
    public const string ACCESS = 'SCHOOL_ACCESS';

    public function __construct(
        private readonly SchoolMembershipRepository $schoolMembershipRepository,
    ) {}

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $attribute === self::ACCESS && $subject instanceof SchoolDto;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        if (!$subject instanceof SchoolDto) {
            return false;
        }

        $school = $subject;

        $user = $token->getUser();
        if (!$user instanceof User) {
            return false;
        }

        return $this->schoolMembershipRepository->existsForUserAndSchool($user->getId(), $school->id);
    }
}
