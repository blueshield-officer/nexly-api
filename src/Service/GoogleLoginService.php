<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Security\GoogleIdTokenVerifier;
use Random\RandomException;

readonly class GoogleLoginService
{
    public function __construct(
        private GoogleIdTokenVerifier    $googleIdTokenVerifier,
        private UserRepository           $userRepository,
        private UserService              $userService,
    ){}

    public function loginWithToken(string $token): ?User
    {
        return $this->login($this->googleIdTokenVerifier->verifyIdToken($token));
    }

    public function loginWithCode(string $code): ?User
    {
        return $this->login($this->googleIdTokenVerifier->verifyCode($code));
    }

    private function login(false | array $googleInfo) : ?User
    {
        if (!$googleInfo) return null;

        $googleId = $googleInfo['sub'];
        $email = $googleInfo['email'];

        // TODO play around later
        $user = $this->userRepository->findOneBy(['googleId' => $googleId]);

        if (!$user) {
            $user = $this->userRepository->findOneBy(['email' => $email]);

            if ($user) {
                $this->userService->linkGoogle($user, $googleId);
            }
            else {
                $user = $this->userService->createUser(
                    email: $googleInfo['email'],
                    googleId: $googleInfo['sub'],
                );
            }
        }
        else {
            $this->userService->updateEmail($user, $email);
        }

        return $user;
    }
}
