<?php

namespace App\Service;

use App\Dto\AuthBootstrapResponse;
use App\Entity\User;
use App\Enum\CookieNameEnum;
use App\Environment\EnvironmentChecker;
use App\Manager\UserRefreshTokenManager;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;

readonly class AuthService
{
    public function __construct(
        private RequestStack $requestStack,
        private EnvironmentChecker $environment,
        private UserRefreshTokenManager $userRefreshTokenManager
    )
    {
    }
    public function createTokenResponse(string $refereshToken, ?AuthBootstrapResponse $payload = null) : JsonResponse
    {
        $cookie = Cookie::create(CookieNameEnum::RefreshToken->value)
            ->withValue($refereshToken)
            ->withHttpOnly(true)
            ->withSecure(true)
            ->withPath('/')
            ->withSameSite($this->environment->isProd() ? 'lax' : 'none')
//            ->withSameSite('lax')
            ->withExpires(new \DateTimeImmutable('+30 days'));

        $response = new JsonResponse($payload ?: ['message' => 'Login successful'], 200);
        $response->headers->setCookie($cookie);

        return $response;
    }

    public function logout(User $user): JsonResponse
    {
        $refreshToken = $this->requestStack->getCurrentRequest()
            ?->cookies->get(CookieNameEnum::RefreshToken->value);

        if ($refreshToken !== null) {
            $this->userRefreshTokenManager->revoke($user, $refreshToken);
        }

        $cookie = Cookie::create(CookieNameEnum::RefreshToken->value)
            ->withValue('')
            ->withHttpOnly(true)
            ->withSecure(true)
            ->withPath('/')
            ->withSameSite($this->environment->isProd() ? 'lax' : 'none')
            ->withExpires(new \DateTimeImmutable('-1 hour'));

        $response = new JsonResponse(['message' => 'Logout successful'], 200);
        $response->headers->setCookie($cookie);

        return $response;
    }
}
