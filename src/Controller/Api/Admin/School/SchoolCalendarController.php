<?php

namespace App\Controller\Api\Admin\School;

use App\Dto\Request\SchoolCalendarPostRequest;
use App\Dto\Request\SchoolCalendarPatchRequest;
use App\Dto\SchoolDto;
use App\Entity\SchoolCalendar;
use App\Manager\SchoolCalendarManager;
use App\Resolver\SchoolCalendarResolver;
use DateTimeImmutable;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/{schoolUuid}/calendar')]
readonly class SchoolCalendarController
{
    public function __construct(
        private SchoolCalendarManager $manager
    ){}

    #[Route('', name: 'app_api_admin_school_calendar', methods: ['GET'])]
    public function calendar(
        SchoolDto                    $school,
        #[MapQueryParameter] ?string $from,
        #[MapQueryParameter] ?string $to,
        SchoolCalendarResolver $schoolCalendarResolver
    ): JsonResponse
    {
        $data = $schoolCalendarResolver->resolve($school, from: $from, to: $to);

        return new JsonResponse($data, 200);
    }

    #[Route('/{ymd}', methods: ['POST'])]
    public function create(
        SchoolDto                                      $school,
        DateTimeImmutable                              $ymd,
        #[MapRequestPayload] SchoolCalendarPostRequest $createSchoolCalendarRequest,
    ): JsonResponse
    {
        $schoolCalendar = $this->manager->create(
            $school->id,
            $ymd,
            $createSchoolCalendarRequest
        );
        return new JsonResponse($schoolCalendar, 201);
    }

    #[Route('/{ymd}', methods: ['PATCH'])]
    public function update(
        SchoolDto                                       $school,
        SchoolCalendar                                  $schoolCalendar,
        #[MapRequestPayload] SchoolCalendarPatchRequest $patchSchoolCalendarRequest,
    ): JsonResponse
    {
        $schoolCalendar = $this->manager->update($school->id, $schoolCalendar, $patchSchoolCalendarRequest);
        return new JsonResponse($schoolCalendar);
    }

    #[Route('/{ymd}', methods: ['DELETE'])]
    public function delete(
        SchoolDto $school,
        SchoolCalendar $schoolCalendar,
    ): JsonResponse
    {
        $data = $this->manager->remove($school->id, $schoolCalendar);
        return new JsonResponse($data, 200);
    }

}
