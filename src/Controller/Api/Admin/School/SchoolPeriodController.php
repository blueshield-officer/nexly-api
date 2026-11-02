<?php

namespace App\Controller\Api\Admin\School;

use App\Dto\Request\SchoolPeriodPostRequest;
use App\Dto\Request\SchoolPeriodPatchRequest;
use App\Dto\SchoolDto;
use App\Entity\SchoolPeriod;
use App\Manager\SchoolPeriodManager;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/{schoolUuid}/periods')]
readonly class SchoolPeriodController
{
    public function __construct(
        private SchoolPeriodManager $manager
    ){}

    #[Route('/{periodUuid}', methods: ['PATCH'])]
    public function update(
        SchoolDto                                     $school,
        SchoolPeriod                                  $period,
        #[MapRequestPayload] SchoolPeriodPatchRequest $request,
    ): JsonResponse
    {
        $period = $this->manager->patch($school->id, $period, $request);
        return new JsonResponse([
            'uuid' => $period->getUuid(),
            'name' => $period->getName(),
        ], 201);
    }

    #[Route('', methods: ['POST'])]
    public function create(
        SchoolDto                                    $school,
        #[MapRequestPayload] SchoolPeriodPostRequest $request,
    ): JsonResponse
    {
        $period = $this->manager->create($school->id, $request->name);
        return new JsonResponse([
            'uuid' => $period->getUuid(),
            'name' => $period->getName(),
        ], 201);
    }

    #[Route('/{periodUuid}', methods: ['DELETE'])]
    public function delete(
        SchoolDto $school,
        SchoolPeriod $period,
    ): JsonResponse
    {
        $this->manager->remove($school->id, $period);
        return new JsonResponse([], 204);
    }
}
