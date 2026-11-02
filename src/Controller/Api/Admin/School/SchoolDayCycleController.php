<?php

namespace App\Controller\Api\Admin\School;

use App\Dto\Request\SchoolDayCyclePatchRequest;
use App\Dto\Request\SchoolDayCyclePostRequest;
use App\Dto\SchoolDto;
use App\Entity\SchoolDayCycle;
use App\Manager\SchoolDayCycleManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/{schoolUuid}/day-cycles')]
readonly class SchoolDayCycleController
{
    public function __construct(
        private SchoolDayCycleManager $manager,
    ){}

    #[Route('', methods: ['POST'])]
    public function create(
        SchoolDto $school,
        #[MapRequestPayload] SchoolDayCyclePostRequest $request,
    ): JsonResponse
    {
        $schoolDayCycles = $this->manager->create($school->id, $request);

        return new JsonResponse($schoolDayCycles, 201);
    }

    #[Route('/{dayCycleUuid}', methods: ['PATCH'])]
    public function update(
        SchoolDto $school,
        SchoolDayCycle $dayCycle,
        #[MapRequestPayload] SchoolDayCyclePatchRequest $request,
    ): JsonResponse
    {
        $schoolDayCycles = $this->manager->update($school->id, $dayCycle, $request);

        return new JsonResponse($schoolDayCycles);
    }

    #[Route('/{dayCycleUuid}', methods: ['DELETE'])]
    public function delete(
        SchoolDto $school,
        SchoolDayCycle $dayCycle,
    ): JsonResponse
    {
        $schoolDayCycles = $this->manager->remove($school->id, $dayCycle);

        return new JsonResponse($schoolDayCycles);
    }
}
