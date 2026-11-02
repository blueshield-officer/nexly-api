<?php

namespace App\Manager;

use App\Dto\Request\SchoolSchedulePeriodPatchRequest;
use App\Dto\Request\SchoolSchedulePatchRequest;
use App\Dto\Request\SchoolSchedulePostRequest;
use App\Dto\Request\Util\PatchFieldChecker;
use App\Entity\School;
use App\Entity\SchoolSchedule;
use App\Entity\SchoolSchedulePeriod;
use App\Repository\SchoolCalendarRepository;
use App\Repository\SchoolPeriodRepository;
use App\Repository\SchoolSchedulePeriodRepository;
use App\Sis\Storage\Calendar\SchoolCalendarStorage;
use App\Sis\Storage\Schedule\SchoolScheduleStorage;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Uid\Uuid;

final readonly class SchoolScheduleManager
{
    public function __construct(
        private EntityManagerInterface         $em,
        private SchoolScheduleStorage          $schoolScheduleStorage,
        private SchoolCalendarStorage          $schoolCalendarStorage,
        private SchoolPeriodRepository         $schoolPeriodRepository,
        private SchoolCalendarRepository       $schoolCalendarRepository,
        private SchoolSchedulePeriodRepository $schoolSchedulePeriodRepository,
    ) {}

    public function create(
        int $schoolId,
        SchoolSchedulePostRequest $request
    ): array
    {
        $schoolRef = $this->em->getReference(School::class, $schoolId);

        $schedule = new SchoolSchedule();
        $schedule->setSchool($schoolRef);
        $schedule->setCode($request->code);
        $schedule->setDescription($request->description);
        $schedule->setIsHidden($request->isHidden);

        $this->em->persist($schedule);
        $this->em->flush();

        $this->schoolScheduleStorage->invalidate($schoolId);

        $schedules = $this->schoolScheduleStorage->get($schoolId);
        $uuid = (string)$schedule->getUuid();

        return [
            ...$schedules[$uuid],
            'uuid' => $uuid
        ];
    }

    public function update(
        int                        $schoolId,
        SchoolSchedule             $schoolSchedule,
        SchoolSchedulePatchRequest $request,
    ) : array
    {
        $changed = false;

        if (PatchFieldChecker::isProvided($request, 'code')) {
            if ($schoolSchedule->getCode() !== $request->code) {
                $schoolSchedule->setCode($request->code);
                $changed = true;
            }
        }
        if (PatchFieldChecker::isProvided($request, 'description')) {
            if ($schoolSchedule->getDescription() !== $request->description) {
                $schoolSchedule->setDescription($request->description);
                $changed = true;
            }
        }

        if (PatchFieldChecker::isProvided($request, 'isHidden')) {
            if ($schoolSchedule->isHidden() !== $request->isHidden) {
                $schoolSchedule->setIsHidden($request->isHidden);
                $changed = true;
            }
        }

        if ($changed) {
            $this->em->flush();
            $this->schoolScheduleStorage->invalidate($schoolId);
        }

        $schedules = $this->schoolScheduleStorage->get($schoolId);

        return $schedules[(string)$schoolSchedule->getUuid()];
    }

    public function addPeriod(
        SchoolSchedule $schoolSchedule,
        Uuid $periodUuid,
        string $timeStart,
        string $timeEnd
    ): array
    {
        // TODO position validation + start end

        $school = $schoolSchedule->getSchool();

        $period = $this->schoolPeriodRepository->findOneBy([
            'uuid' => $periodUuid,
            'school' => $school,
        ]);

        if ($period === null) {
            throw new NotFoundHttpException('Period not found for this school.');
        }

        $start = DateTimeImmutable::createFromFormat('H:i', $timeStart);
        $end = DateTimeImmutable::createFromFormat('H:i', $timeEnd);

        if ($start === false || $end === false) {
            throw new BadRequestHttpException('Invalid time format, expected H:i.');
        }

        if ($start > $end) {
            throw new BadRequestHttpException('Start time cannot be less than end time.');
        }

        $schoolSchedulePeriod = new SchoolSchedulePeriod();
        $schoolSchedulePeriod->setPeriod($period);
        $schoolSchedulePeriod->setStartTime($start);
        $schoolSchedulePeriod->setEndTime($end);

        $this->em->persist($schoolSchedulePeriod);

        $schoolSchedule->addSchoolSchedulePeriod($schoolSchedulePeriod);

        $this->em->flush();

        $this->schoolScheduleStorage->invalidate($school->getId());

        $schedules = $this->schoolScheduleStorage->get($school->getId());

        return $schedules[(string)$schoolSchedule->getUuid()];
    }

    public function updatePeriod(
        int                              $schoolId,
        SchoolSchedulePeriod             $schoolSchedulePeriod,
        SchoolSchedulePeriodPatchRequest $request,
    )
    {
        $changed = false;

        if (PatchFieldChecker::isProvided($request, 'periodUuid')) {
            $schoolPeriod = $this->schoolPeriodRepository->findOneBySchoolIdAndPeriodUuidOrFail($schoolId, $request->periodUuid);
            if ((string)$schoolSchedulePeriod->getPeriod()->getUuid() !== (string)$request->periodUuid) {
                $schoolSchedulePeriod->setPeriod($schoolPeriod);
                $changed = true;
            }
        }

        if (PatchFieldChecker::isProvided($request, 'startTime')) {
            if ($schoolSchedulePeriod->getStartTime()->format("H:i")  !== $request->startTime)
            {
                $schoolSchedulePeriod->setStartTime(DateTimeImmutable::createFromFormat('H:i', $request->startTime));
                $changed = true;
            }
        }

        if (PatchFieldChecker::isProvided($request, 'endTime')) {
            if ($schoolSchedulePeriod->getEndTime()->format("H:i")  !== $request->endTime)
            {
                $schoolSchedulePeriod->setEndTime(DateTimeImmutable::createFromFormat('H:i', $request->endTime));
                $changed = true;
            }
        }

        if ($changed) {
            $this->em->flush();
            $this->schoolScheduleStorage->invalidate($schoolId);
        }

        $schedules = $this->schoolScheduleStorage->get($schoolId);
        return $schedules[(string)$schoolSchedulePeriod->getSchedule()->getUuid()];
    }

    public function removePeriod(int $schoolId, SchoolSchedulePeriod $schoolSchedulePeriod)
    {
        $this->em->remove($schoolSchedulePeriod);
        $this->em->flush();
        $this->schoolScheduleStorage->invalidate($schoolId);

        return $this->schoolScheduleStorage->get($schoolId)[(string)$schoolSchedulePeriod->getSchedule()->getUuid()];
    }

    public function remove(int $schoolId, SchoolSchedule $schoolSchedule): void
    {
        $affectedDates = $this->schoolCalendarRepository->clearScheduleForAll($schoolSchedule);
        $this->schoolSchedulePeriodRepository->deleteAllBySchedule($schoolSchedule);

        $this->em->remove($schoolSchedule);
        $this->em->flush();

        $this->schoolScheduleStorage->invalidate($schoolId);
        foreach ($affectedDates as $affectedDate) {
            $this->schoolCalendarStorage->invalidate($schoolId, $affectedDate);
        }
    }
}
