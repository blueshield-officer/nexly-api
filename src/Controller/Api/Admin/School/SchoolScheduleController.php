<?php

namespace App\Controller\Api\Admin\School;

use App\Dto\Request\SchoolSchedulePeriodPostRequest;
use App\Dto\Request\SchoolSchedulePostRequest;
use App\Dto\Request\SchoolSchedulePeriodPatchRequest;
use App\Dto\Request\SchoolSchedulePatchRequest;
use App\Dto\SchoolDto;
use App\Entity\SchoolSchedule;
use App\Entity\SchoolSchedulePeriod;
use App\Manager\SchoolScheduleManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/{schoolUuid}/schedules')]
readonly class SchoolScheduleController
{
    public function __construct(
        private SchoolScheduleManager $manager,
    ) {}
    #[Route('', methods: ['POST'])]
    public function create(
        SchoolDto                                      $school,
        #[MapRequestPayload] SchoolSchedulePostRequest $request,
    ): JsonResponse
    {
        $schedule = $this->manager->create($school->id, $request);
        return new JsonResponse($schedule, 201);
    }

    #[Route('/{scheduleUuid}', methods: ['PATCH'])]
    public function update(
        SchoolDto                                       $school,
        SchoolSchedule                                  $schedule,
        #[MapRequestPayload] SchoolSchedulePatchRequest $request,
    ): JsonResponse
    {
        $schedule = $this->manager->update($school->id, $schedule, $request);
        return new JsonResponse($schedule);
    }

    #[Route('/{scheduleUuid}/periods', methods: ['POST'])]
    public function addPeriod(
        SchoolSchedule                                       $schedule,
        #[MapRequestPayload] SchoolSchedulePeriodPostRequest $request,
    ): JsonResponse
    {
        $schedule = $this->manager->addPeriod(
            $schedule,
            $request->periodUuid,
            $request->startTime,
            $request->endTime,
        );
        return new JsonResponse($schedule);
    }
    #[Route('/{scheduleUuid}/periods/{schedulePeriodUuid}', methods: ['PATCH'])]
    public function updatePeriod(
        SchoolDto                                             $schoolDto,
        SchoolSchedulePeriod                                  $schedulePeriod,
        #[MapRequestPayload] SchoolSchedulePeriodPatchRequest $request,
    ) : JsonResponse
    {
        $schedule = $this->manager->updatePeriod(
            $schoolDto->id,
            $schedulePeriod,
            $request
        );

        return new JsonResponse($schedule);
    }


    #[Route('/{scheduleUuid}/periods/{schedulePeriodUuid}', methods: ['DELETE'])]
    public function removePeriod(
        SchoolDto $schoolDto,
        SchoolSchedulePeriod $schedulePeriod,
    ): JsonResponse
    {
        $schedule = $this->manager->removePeriod(
            $schoolDto->id,
            $schedulePeriod,
        );

        return new JsonResponse($schedule);
    }

    #[Route('/{scheduleUuid}', methods: ['DELETE'])]
    public function delete(
        SchoolDto $school,
        SchoolSchedule $schedule,
    ): JsonResponse
    {
        $this->manager->remove($school->id, $schedule);
        return new JsonResponse([], 204);
    }
}
