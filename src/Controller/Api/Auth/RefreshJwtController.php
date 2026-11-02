<?php

namespace App\Controller\Api\Auth;

use App\Enum\CookieNameEnum;
use App\Security\JwtGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class RefreshJwtController extends AbstractController
{
    #[Route('/refresh-jwt', name: 'app_api_auth_refres-jwt', methods: ['POST'])]
    public function refreshJwt(
        Request            $request,
        JwtGenerator       $jwtTokenGenerator,
    ) : JsonResponse
    {
        $refreshToken = $request->cookies->get(CookieNameEnum::RefreshToken->value);

        if (!$refreshToken) return new JsonResponse(['error' => 'No refresh token set'], 400);

        $jwt = $jwtTokenGenerator->generateFromRefreshToken($refreshToken);

        if (!$jwt){
            $response = new JsonResponse(['error' => 'Invalid refresh token'], 401);
            $response->headers->clearCookie(CookieNameEnum::RefreshToken->value);
            return $response;
        }

        return new JsonResponse([
            'token' => $jwt,
        ], 200);
    }
}
