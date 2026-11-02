<?php

namespace App\Controller\Api\Auth;

use App\Dto\AuthBootstrapResponse;
use App\Exception\BadRequest\InvalidGoogleTokenException;
use App\Manager\UserRefreshTokenManager;
use App\Security\JwtGenerator;
use App\Service\AuthService;
use App\Service\GoogleLoginService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class GoogleAuthController extends AbstractController
{
    #[Route('/google/onetap', name: 'app_api_auth_google_onetap', methods: ['POST'])]
    public function oneTap(
        Request $request,
        GoogleLoginService $googleLoginService,
        AuthService $authService,
        UserRefreshTokenManager $userRefreshTokenManager,
        JwtGenerator $jwtGenerator,
    ): Response
    {
        $wantsPayload = $request->query->getBoolean('payload');

        $token = json_decode($request->getContent(), true)['token'];

        if (!$token) throw new InvalidGoogleTokenException();

        $user = $googleLoginService->loginWithToken($token);

        if (is_null($user)) throw new InvalidGoogleTokenException();

        if ($wantsPayload){
            $payload = new AuthBootstrapResponse(
                jwt: $jwtGenerator->generateFromUser($user),
                preferences: $user->getAppPreferences(),
                states: $user->getAppStates()
            );
        }

        $refreshToken = $userRefreshTokenManager->generate($user);

        return $authService->createTokenResponse($refreshToken, $payload ?? null);
    }

    #[Route('/google/callback', name: 'app_api_auth_google_callback')]
    public function callBack(Request $request, GoogleLoginService $googleLoginService): JsonResponse
    {
        $code = $request->query->get('code') ?? $request->toArray()['code'] ?? null;

        if (!$code) return new JsonResponse(['error' => 'Missing code'], 400);

        $user = $googleLoginService->loginWithCode($code);

        if (is_null($user)) return new JsonResponse(['error' => 'Invalid token'], 400);

        return new JsonResponse(['success' => true]);
    }
}
