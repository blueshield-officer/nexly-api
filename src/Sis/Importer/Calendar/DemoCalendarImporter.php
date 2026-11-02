<?php

namespace App\Sis\Importer\Calendar;

use App\Dto\SchoolDto;
use DateTimeImmutable;

class DemoCalendarImporter extends AbstractCalendarImporter
{
    public function import(SchoolDto $school, DateTimeImmutable $from, DateTimeImmutable $to): void
    {
        foreach ($this->dateHelper->getDateRange($from, $to) as $date){
           $existing = $this->storage->get($school->id, $date);

           if (is_null($existing)) {
               // suppose to be fetched
               $raw       = $this->generateDateInfo($date);
               $formatted = $this->formatter->format($raw);

               $this->storage->save($school->id, $date, $formatted);
           }
        }
    }

    private function generateDateInfo(DateTimeImmutable $date) : ?array
    {
        if ($date->format('w') == 0 || $date->format('w') == 6) return null;
        $rand = rand(0, 100);

        $dayOff = $this->generateDayOff();
        $scheduleId = is_null($dayOff) ? ($rand > 90 ? 'c' : ($rand > 60 ? 'b' : 'a')) : null;


        return [
            'scheduleId' => $scheduleId,
            'dayOff' => $dayOff
        ];
    }

    private function generateDayOff() : ?array
    {
        $chance = rand(0, 100);
        if ($chance > 90) $scope = null;
        else if ($chance > 70){
            $availableGrades = ['9', '10', '11', '12'];
            $scope = [];
            foreach ($availableGrades as $grade) {
                $chance = rand(0, 100);
                if ($chance > 50) {
                    $scope[] = $grade;
                }
            }
            if (count($scope) == 0){
                $scope[] = $availableGrades[array_rand($availableGrades)];
            };
        }
        else {
            return null;
        }

        return [
            'scope' => $scope,
            'title' => $this->generateDayOffTitle(),
            'description' => 'Really important day which everybody celebrates.',
        ];
    }

    private function generateDayOffTitle() : ?string
    {
        $days = [
            "Asphalt", "Cement", "Stapler", "Parking Lot", "Ceiling Tile",
            "Fire Drill", "Pencil Sharpener", "Hall Pass", "Cafeteria Tray",
            "Locker Jam", "Broken Projector", "Substitute Teacher", "Pop Quiz",
            "Vending Machine", "Gym Whistle", "Detention Slip", "Report Card",
            "Chalk Dust", "Binder Clip", "Overhead Projector", "Eraser Dust",
            "Bathroom Pass", "Flagpole", "Speed Bump", "Crosswalk",
            "Drinking Fountain", "Paper Jam", "Bell Schedule", "Wet Paint",
        ];

        return $days[array_rand($days)] . 'Day';
    }
}
