<?php

namespace App\Security;

use App\Entity\User;
use App\Repository\UserRefreshTokenRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

readonly class JwtGenerator
{
    public function __construct(
        private UserRefreshTokenRepository $userRefreshTokenRepository,
        private JWTTokenManagerInterface $jwtManager
    ){}

    public function generateFromRefreshToken(string $rawToken): ?string
    {
        $hashed = hash('sha256', $rawToken);

        $userRefreshToken = $this->userRefreshTokenRepository->findOneByToken($hashed);

        if (!$userRefreshToken) return null;

        return $this->jwtManager->create($userRefreshToken->getUser());
    }

    public function generateFromUser(User $user): ?string
    {
        return $this->jwtManager->create($user);
    }

}
