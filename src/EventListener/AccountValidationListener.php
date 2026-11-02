<?php

namespace App\EventListener;

use App\Entity\User;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Event\CheckPassportEvent;

#[AsEventListener(event: CheckPassportEvent::class)]
Final class AccountValidationListener
{
    public function __invoke(CheckPassportEvent $event): void
    {
        $passport = $event->getPassport();

        if (!$passport->hasBadge(PasswordCredentials::class)) {
            return;
        }

        $user = $passport->getUser();

        if (!$user instanceof User) return;

        if (!$user->getPassword()) {
            throw new CustomUserMessageAuthenticationException(
                'This account was created via Google. Please sign in with Google or try settings up password.'
            );
        }
    }
}
