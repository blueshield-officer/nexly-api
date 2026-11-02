<?php

namespace App\Manager;

use App\Dto\Request\SchoolCalendarPostRequest;
use App\Dto\Request\SchoolCalendarPatchRequest;
use App\Dto\Request\Util\PatchFieldChecker;
use App\Entity\School;
use App\Entity\SchoolCalendar;
use App\Entity\SchoolCalendarDayOff;
use App\Entity\SchoolGrade;
use App\Enum\SchoolCalendarDayStatusEnum;
use App\Exception\SchoolCalendarAlreadyExistsException;
use App\Repository\SchoolCalendarDayOffRepository;
use App\Repository\SchoolCalendarRepository;
use App\Repository\SchoolDayCycleRepository;
use App\Repository\SchoolGradeRepository;
use App\Repository\SchoolScheduleRepository;
use App\Sis\Storage\Calendar\SchoolCalendarStorage;
use App\Utils\UuidUtils;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Uid\Uuid;

final readonly class SchoolCalendarManager
{
    public function __construct(
        private EntityManagerInterface $em,
        private SchoolCalendarStorage $storage,
        private SchoolCalendarRepository $calendarRepository,
        private SchoolGradeRepository $gradeRepository,
        private SchoolScheduleRepository $scheduleRepository,
        private SchoolDayCycleRepository $dayCycleRepository,
    ) {}

    public function create(
        int                       $schoolId,
        DateTimeImmutable         $date,
        SchoolCalendarPostRequest $request,
    ): array
    {
        $existing = $this->calendarRepository->findOneBySchoolAndDate($schoolId, $date);
        if ($existing !== null) {
            throw new SchoolCalendarAlreadyExistsException($schoolId, $date);
        }

        $schedule = $request->scheduleUuid !== null
            ? $this->scheduleRepository->findOneBySchoolIdAndScheduleUuidOrFail($schoolId, Uuid::fromString($request->scheduleUuid))
            : null;


        $schoolRef = $this->em->getReference(School::class, $schoolId);

        $schoolCalendar = new SchoolCalendar();
        $schoolCalendar->setSchool($schoolRef);
        $schoolCalendar->setDate($date);
        $schoolCalendar->setSchedule($schedule);
        $schoolCalendar->setSchoolScheduled($request->isSchoolDay ?? false);
        $schoolCalendar->setDayCycleAutofill($request->dayCycleAutofill ?? true);
        if ($request->dayCycleUuid !== null) {
            $schoolCalendar->setDayCycleAutofill(false);
            $schoolCalendar->setDayCycle(
                $this->dayCycleRepository->findOneBySchoolIdAndUuid($schoolId, Uuid::fromString($request->dayCycleUuid))
            );
        }
        $schoolCalendar->setAutoChange(false);

        $this->em->persist($schoolCalendar);

        // TODO add check
        if ($request->dayOff !== null) {
            $this->applyDayOff($schoolCalendar, $schoolId, $request->dayOff);
        }

        $this->em->flush();

        $this->storage->invalidate($schoolId, $date);

        return $this->storage->get($schoolId, $date);
    }

    public function update(int $schoolId, SchoolCalendar $schoolCalendar, SchoolCalendarPatchRequest $request): array
    {
        $date = $schoolCalendar->getDate();
        $changed = false;

        if (PatchFieldChecker::isProvided($request, 'scheduleUuid')) {
            $oldSchedule = $schoolCalendar->getSchedule()?->getUuid();
            if (!is_null($oldSchedule)) $oldSchedule = (string)$oldSchedule;
            if ($oldSchedule !== $request->scheduleUuid) {
                $schedule = $request->scheduleUuid !== null
                    ? $this->scheduleRepository->findOneBySchoolIdAndScheduleUuidOrFail($schoolId, Uuid::fromString($request->scheduleUuid))
                    : null;
                $schoolCalendar->setSchedule($schedule);
                $changed = true;
                // test
            }
        }

        if (PatchFieldChecker::isProvided($request, 'dayOff')) {
            // TODO add check
            $this->applyDayOff($schoolCalendar, $schoolId, $request->dayOff);
            $changed = true;
        }

        if (PatchFieldChecker::isProvided($request, 'schoolScheduled')) {
            if ($request->schoolScheduled !== $schoolCalendar->isSchoolDay()) {
                $schoolCalendar->setSchoolScheduled($request->schoolScheduled);
                $changed = true;
            }
        }

        if (PatchFieldChecker::isProvided($request, 'dayCycleUuid')) {
            if ($request->dayCycleUuid !== (string)$schoolCalendar->getSchedule()?->getUuid()) {
                $schoolCalendar->setDayCycle(
                    $request->dayCycleUuid !== null ? $this->dayCycleRepository->findOneBySchoolIdAndUuid($schoolId, Uuid::fromString($request->dayCycleUuid)) : null
                );
                $changed = true;
            }
        }

        if ($changed) {
            $this->em->flush();
            $this->storage->invalidate($schoolId, $date);
        }

        return $this->storage->get($schoolId, $date);
    }

    public function remove(int $schoolId, SchoolCalendar $schoolCalendar)
    {
        $date = $schoolCalendar->getDate();

        $this->em->remove($schoolCalendar);
        $this->em->flush();

        $this->storage->invalidate($schoolId, $date);

        return $this->storage->get($schoolId, $date);
    }

    private function applyDayOff(SchoolCalendar $calendar, int $schoolId, ?array $dayOffData): void
    {
        $existing = $calendar->getDayOff();

        if ($dayOffData === null) {
            $calendar->setDayOff(null);
            return;
        }

        if ($existing === null) {
            $existing = new SchoolCalendarDayOff();
            $calendar->setDayOff($existing);
            $this->em->persist($existing);
        }

        $existing->setTitle($dayOffData['title'] ?? null);
        $existing->setDescription($dayOffData['description'] ?? null);

        $this->syncScope($existing, $schoolId, $dayOffData['scope'] ?? null);
    }

    private function syncScope(SchoolCalendarDayOff $dayOff, int $schoolId, null|array $gradeUuids): void
    {
        if ($gradeUuids === null) {
            foreach ($dayOff->getScope() as $currentGrade) {
                $dayOff->removeScope($currentGrade);
            }
            return;
        }

        $desiredGrades = $this->gradeRepository->findByUuidsForSchoolOrFail($schoolId, $gradeUuids);
        $desiredIds = array_map(static fn(SchoolGrade $g) => $g->getId(), $desiredGrades);

        foreach ($dayOff->getScope() as $currentGrade) {
            if (!in_array($currentGrade->getId(), $desiredIds, true)) {
                $dayOff->removeScope($currentGrade);
            }
        }

        foreach ($desiredGrades as $grade) {
            $dayOff->addScope($grade);
        }
    }
}
