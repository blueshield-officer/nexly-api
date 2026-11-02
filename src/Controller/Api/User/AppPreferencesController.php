<?php

namespace App\Controller\Api\User;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class AppPreferencesController extends AbstractController
{
    #[Route('/preferences', name: 'app_api_user_preferences_get', methods: ["GET"])]
    public function preferences(): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();

        return new JsonResponse([
            'preferences' => $user->getAppPreferences() ?? []
        ]);
    }

    #[Route('/preferences', name: 'app_api_user_preferences_update', methods: ["PATCH"])]
    public function updatePreferences(
        Request $request,
        EntityManagerInterface $em,
    ): JsonResponse
    {
        $content = $request->getContent();

        if (empty($content))                                return new JsonResponse(['error' => 'Empty Body'], 400);
        if (strlen($content) > 10240)                       return new JsonResponse(['error' => 'Payload too large'], 400);

        $data = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE)          return new JsonResponse(['error' => 'Invalid JSON'], 400);
        if (empty($data) )                                  return new JsonResponse(['error' => 'No Data Provided'], 400);

        /** @var User $user */
        $user = $this->getUser();
        $user->setAppPreferences($data);
        $em->flush();

        return new JsonResponse(['success' => true]);
    }
}
