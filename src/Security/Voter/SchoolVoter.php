<?php

namespace App\Security\Voter;

use App\Dto\SchoolDto;
use App\Entity\School;
use App\Repository\SchoolRepository;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

final class SchoolVoter extends Voter
{
    public const string USER = 'ROLE_USER';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return $attribute === self::USER && $subject instanceof SchoolDto;

//        return in_array($attribute, [self::EXIST, self::VIEW])
//            && $subject instanceof \App\Entity\School;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        if ($subject->private)
        {
            $user = $token->getUser();
            if ($user instanceOf UserInterface)
            {
                return false;
            }
        }

        return true;
    }
}
