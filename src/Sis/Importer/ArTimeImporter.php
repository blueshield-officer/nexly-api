<?php

namespace App\Sis\Importer;

use App\Dto\SchoolCalendarDTO;
use App\Dto\SchoolDto;
use DateTimeImmutable;
use DateTimeInterface;

class ArTimeImporter extends AbstractSisImporter
{
    public function calendar(
        SchoolDto         $school,
        DateTimeInterface $fromDate,
        DateTimeInterface $currentDate,
        DateTimeInterface $toDate
    ): array
    {
        $allGrades = array_map(fn($grade) => (string)$grade, array_keys($school->grades));
        $this->setDateInterval($fromDate, $currentDate, $toDate);
        $this->response = $this->getCachedDays();

        if (count($this->dates) > 0)
        {
            $timezone = $currentDate->getTimezone();
            foreach (
                $this->fetchResource('https://ar-time.loc/api/calendar?dates=' . join(',', array_map(fn($date) => $date->format('Y-m-d'), $this->dates)))
                as $date => $item
            ) {
                $date = DateTimeImmutable::createFromFormat("Y-m-d", $date, $timezone);

                $schoolCalendarDTO = SchoolCalendarDTO::fromArray([
                    'date' => $date,
                    'scheduleId' => $item['schedule'],
                    'autoChange' => true,
                    'dayOff' => isset($item['noSchool']) ? [
                        'scope' => $item['noSchool'] === $allGrades ? null : $item['noSchool'],
                        'title' => null,
                        'description' => null,
                    ] : null,
                ]);

                $this->cacheDay($schoolCalendarDTO);
            }
            $this->entityManager->flush();
        }

        return $this->response;
    }

    public function schedules() : array
    {
//        $this->response = $this->getCachedSchedules();

        // also remove, plug
        function timeToSeconds(string $time) : int
        {
            [$hours, $minutes, $seconds] = explode(":", $time);
            $hours = intval($hours);
            $minutes = intval($minutes);
            $seconds = intval($seconds);

            return
                $seconds                +
                $minutes    * 60        +
                $hours      * 60 * 60   ;
        }

        $schedules = $this->fetchResource('https://ar-time.loc/api/schedules');

        foreach ($schedules as $scheduleId => $schedule) {
            foreach ($schedule["periods"] as $periodPosition => $period)
            {
                $startTime = $this->unixSecondsToTime($period["start"]);
                $endTime = $this->unixSecondsToTime($period["end"]);

                unset($schedules[$scheduleId]["periods"][$periodPosition]['start']);
                unset($schedules[$scheduleId]["periods"][$periodPosition]['end']);

                $schedules[$scheduleId]["periods"][$periodPosition]['startTime'] = $startTime;
                $schedules[$scheduleId]["periods"][$periodPosition]['endTime'] = $endTime;
            }
        }

        // TODO move schedule cleance
        $newSchedules = [];
        foreach ($schedules as $scheduleId => $schedule) {
            $scheduleId = strtoupper($scheduleId);
            foreach ($schedule["periods"] as $periodPosition => $period) {
                if (!is_null($period["endTime"])) {
                    if (
                        timeToSeconds($period["endTime"]) - timeToSeconds($period["startTime"]) <= 60
                    ) {
                        $newSchedules[$scheduleId]["periods"][$periodPosition - 1]["endTime"] = $period["endTime"];
                    }
                    else {
                        if (!isset($newSchedules[$scheduleId]["periods"])) {
                            $newSchedules[$scheduleId]["description"] = $schedule["description"];
                            $newSchedules[$scheduleId]["periods"] = [];
                        }
                        $newSchedules[$scheduleId]["periods"][$periodPosition] = $period;
                    }
                }
            }

            $newSchedules[$scheduleId]["periods"] = array_combine(
                range(0, count($newSchedules[$scheduleId]["periods"]) - 1),
                array_values($newSchedules[$scheduleId]["periods"])
            );
        }

        return $newSchedules;

//        return $this->response;
    }

    ////////////////////////////
    ///
    ///  SHITCODE ALERT
    ///
    /// ////////////////////////
    private function unixSecondsToTime(int | null $unixSeconds) : string | null
    {
        if (is_null($unixSeconds)) return null;
        $hours = floor($unixSeconds / 3600);
        $minutes = floor(($unixSeconds - ($hours * 3600)) / 60);
        return $this->addZeroIfLessThan10($hours) . ":" . $this->addZeroIfLessThan10($minutes) . ":00";
    }

    private function addZeroIfLessThan10(int $number) : string
    {
        if ($number < 10) return "0" . $number;
        return $number;
    }
}
