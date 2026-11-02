<?php

namespace App\Manager;

use App\Entity\User;
use App\Entity\UserRefreshToken;
use App\Repository\UserRefreshTokenRepository;
use Doctrine\ORM\EntityManagerInterface;
use Random\RandomException;

readonly class UserRefreshTokenManager
{
    public function __construct(
        private EntityManagerInterface     $em,
        private UserRefreshTokenRepository $repository
    ){}

    /**
     * @throws RandomException
     */
    public function generate(User $user): string
    {
        $refreshToken = bin2hex(random_bytes(32));

        $userRefreshToken = new UserRefreshToken();
        $userRefreshToken->setUser($user);
        $userRefreshToken->setToken(hash('sha256', $refreshToken));
        $userRefreshToken->setExpiresAt(new \DateTimeImmutable('+30 days'));

        $this->em->persist($userRefreshToken);
        $this->em->flush();

        return $refreshToken;
    }

    public function revoke(User $user, string $rawToken): void
    {
        $hashed = hash('sha256', $rawToken);
        $token = $this->repository->findOneByUserAndToken($user, $hashed);

        if ($token !== null) {
            $this->em->remove($token);
            $this->em->flush();
        }
    }
}
