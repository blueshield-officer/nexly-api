<?php

namespace App\Security;

use App\Entity\User;
use App\Manager\UserRefreshTokenManager;
use App\Service\AuthService;
use Google\Service\ServiceControl\Auth;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

readonly class AuthenticationSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    public function __construct(
        private UserRefreshTokenManager $userRefreshTokenManager,
        private AuthService $authService,
    ) {}

    public function onAuthenticationSuccess(Request $request, TokenInterface $token): JsonResponse
    {
        $user = $token->getUser();
        assert($user instanceof User);

        $token = $this->userRefreshTokenManager->generate($user);

        return $this->authService->createTokenResponse($token, null);
    }
}
