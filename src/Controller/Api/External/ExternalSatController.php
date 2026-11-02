<?php

namespace App\Controller\Api\External;

use App\Service\Cache\ExternalSatCache;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class ExternalSatController extends AbstractController
{
    #[Route('/sat', name: 'app_external_sat')]
    public function index(
        ExternalSatCache $externalSatCache
    ): JsonResponse
    {
        return new JsonResponse($externalSatCache->getAll());
    }
}
