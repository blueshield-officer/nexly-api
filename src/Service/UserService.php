<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

readonly class UserService
{
    public function __construct(
        private UserPasswordHasherInterface     $hasher,
        private UserRepository         $userRepository,
        private EntityManagerInterface $em,
    )
    {}

    public function createUser(string $email, ?string $password = null, ?string $googleId = null) : User
    {
        if ($this->userRepository->findOneBy(['email' => $email])) {
            throw new \DomainException('user_exists');
        }

        $user = new User();
        $user->setEmail($email);
        if ($googleId) $user->setGoogleId($googleId);
        if ($password) $user->setPassword($this->hasher->hashPassword($user, $password));
        $user->setRoles(['ROLE_USER']);

        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }

    public function updateEmail(User $user, string $email) : void
    {
        if ($user->getEmail() === $email) return;

        $user->setEmail($email);
        $this->em->flush();
    }

    public function linkGoogle(User $user, string $googleId) : void
    {
        if ($user->getGoogleId() === $googleId) return;

        $user->setGoogleId($googleId);
        $this->em->flush();
    }

    public function userExists(string $email) : bool
    {
        return (bool) $this->userRepository->findOneBy(['email' => $email]);
    }
}
