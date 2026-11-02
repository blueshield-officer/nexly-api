<?php

namespace App\Controller\Api\School;

use App\Dto\SchoolDto;
use App\Factory\SchoolCacheFactory;
use App\Resolver\SchoolCalendarResolver;
use App\Sis\Resolver\SisCalendarImporterResolver;
use App\Sis\Storage\Calendar\SchoolCalendarStorage;
use App\Sis\Storage\DayCycle\SchoolDayCycleStorage;
use App\Sis\Storage\Schedule\SchoolPeriodStorage;
use App\Sis\Storage\Schedule\SchoolScheduleStorage;
use DateTimeImmutable;
use DateTimeZone;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class SchoolController extends AbstractController
{
    #[Route(['/{identifier}', '/'], name: 'app_api_school_search')]
    public function search(
        SchoolDto $schoolDto,
    ): JsonResponse
    {
        return new JsonResponse($schoolDto->jsonSerialize());
    }

    #[Route('/{schoolUuid}/config', name: 'app_school_config')]
    #[IsGranted('ROLE_USER', subject: 'school')]
    public function config(
        SchoolDto          $school,
        SchoolCacheFactory $schoolCacheFactory,
    ): JsonResponse
    {
        $schoolCache = $schoolCacheFactory->create($school->id);
        return new JsonResponse($schoolCache->getConfig(), 200, array());
    }

    #[Route('/{schoolUuid}/calendar/events', name: 'app_school_calendar_events_upcoming')]
    #[IsGranted('ROLE_USER', subject: 'school')]
    public function calendarEvents(
        SchoolDto $school,
        SchoolCacheFactory $schoolCacheFactory,
    ){
        return new JsonResponse();

        $today = new DateTimeImmutable('now', new DateTimeZone($school->timezone));

        $schoolCache = $schoolCacheFactory->create($school->id);
        return new JsonResponse($schoolCache->getUpcomingCalendarEvents($today), 200, array());
    }

    #[Route('/{schoolUuid}/calendar/structure', name: 'app_school_calendar_structure')]
    #[IsGranted('ROLE_USER', subject: 'school')]
    public function calendarStructure(
        SchoolDto $school,
        SchoolCacheFactory $schoolCacheFactory,
    ){
        $schoolCache = $schoolCacheFactory->create($school->id);
        return new JsonResponse(
            $schoolCache->getCalendarStructure()
        );
    }

    #[Route('/{schoolUuid}/notifications', name: 'app_school_notifications')]
    #[IsGranted('ROLE_USER', subject: 'school')]
    public function notifications(
        SchoolDto $school,
        SchoolCacheFactory $schoolCacheFactory,
    ): JsonResponse
    {
        $schoolCache = $schoolCacheFactory->create($school->id);
        return new JsonResponse($schoolCache->getNotifications($school->districtId), 200);
    }

    #[Route('/{schoolUuid}/calendar', name: 'app_school_calendar', methods: ['GET'])]
    #[IsGranted('ROLE_USER', subject: 'school')]
    public function calendar(
        SchoolDto                    $school,
        #[MapQueryParameter] ?string $from,
        #[MapQueryParameter] ?string $to,
        SchoolCalendarResolver $schoolCalendarResolver
    ): JsonResponse
    {
        $data = $schoolCalendarResolver->resolve($school, from: $from, to: $to);

        // TODO do something with this
        $data = array_map(static function (array $day): array {
            unset($day['hasRecord']);
            return $day;
        }, $data);

        return new JsonResponse($data, 200, array());
    }

    #[Route('/{schoolUuid}/schedules', name: 'app_school_schedule')]
    #[IsGranted('ROLE_USER', subject: 'school')]
    public function schedules(
        SchoolDto             $school,
        SchoolScheduleStorage $storage,
    ): JsonResponse
    {
        return new JsonResponse(
            $storage->get($school->id),
            200
        );
    }

    #[Route('/{schoolUuid}/periods', name: 'app_school_periods')]
    #[IsGranted('ROLE_USER', subject: 'school')]
    public function periods(
        SchoolDto             $school,
        SchoolPeriodStorage   $storage,
    ): JsonResponse
    {
        return new JsonResponse(
            $storage->get($school->id),
            200
        );
    }

    #[Route('/{schoolUuid}/day-cycles', name: 'app_school_day-cycles')]
    #[IsGranted('ROLE_USER', subject: 'school')]
    public function dayCycles (
        SchoolDto $school,
        SchoolDayCycleStorage $storage,
    ){
        return new JsonResponse(
            $storage->get($school->id),
            200
        );
    }
}
