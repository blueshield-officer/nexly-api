<?php

namespace App\Controller\Api\Auth;

use App\Entity\User;
use App\Service\AuthService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class LogoutController extends AbstractController
{
    #[Route('/logout', name: 'app_api_auth_logout', methods: ['POST'])]
    public function logout(
        AuthService $authService,
    ): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        return $authService->logout($user);
    }
}
