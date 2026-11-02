<?php

namespace App\Controller;

use App\Service\HealthChecker;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HealthController extends AbstractController
{
    #[Route('/', name: 'app_root', methods: ['GET'])]
    public function index(): JsonResponse
    {
        return new JsonResponse([
            'name' => 'Nexly API',
            'description' => 'This website serves as API resource. You will find no pages here. If you are here by an error, please go to https://nexly.school',
            'status' => 'ok',
        ]);
    }

    #[Route('/healthcheck', name: 'app_healthcheck', methods: ['GET'])]
    public function healthcheck(
        HealthChecker $healthChecker,
    ): JsonResponse
    {
        $checks = $healthChecker->check();
        $healthy = !in_array(false, $checks, true);

        return new JsonResponse(
            ['status' => $healthy ? 'ok' : 'unhealthy'],
            $healthy ? 200 : 503
        );
    }


    #[Route('/favicon.ico', name: 'favicon', methods: ['GET'])]
    public function favicon(): Response
    {
        return new Response('', 204);
    }

}
