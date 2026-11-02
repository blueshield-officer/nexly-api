<?php

namespace App\Controller\Api\User;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class AppStateController extends AbstractController
{
    #[Route('/states', name: 'app_api_user_states_get', methods: ["GET"])]
    public function state(): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();

        return new JsonResponse([
            'states' => $user->getAppStates() ?? []
        ]);
    }

    #[Route('/states', name: 'app_api_user_states_update', methods: ["PATCH"])]
    public function updateState(
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
        $user->setAppStates($data);
        $em->flush();

        return new JsonResponse(['success' => true]);
    }
}
