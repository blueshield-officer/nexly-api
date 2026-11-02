<?php

namespace App\DataFixtures;

use App\Entity\District;
use App\Entity\School;
use App\Entity\SchoolColor;
use App\Entity\SchoolConfig;
use App\Entity\SchoolEmailDomain;
use App\Entity\SchoolGrade;
use App\Entity\SchoolSchedule;
use App\Entity\Sis;
use App\Enum\SisProviderEnum;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Uid\Uuid;

class DistrictSchoolFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        function loadArhs ($manager): void
        {
            $schoolEmailDomain1 = new SchoolEmailDomain();
            $schoolEmailDomain1->setDomain('arhs.org');

            $schoolEmailDomain2 = new SchoolEmailDomain();
            $schoolEmailDomain2->setDomain('ryanhs.org');

            // colors
            $schoolColorBase = new SchoolColor();
            $schoolColorBase->setBase(true);
            $schoolColorBase->setHex("ff0000");
            $schoolColor1 = new SchoolColor();
            $schoolColor1->setBase(false);
            $schoolColor1->setHex("ffff00");
            $schoolColor2 = new SchoolColor();
            $schoolColor2->setBase(false);
            $schoolColor2->setHex("000000");

            $sis = new Sis();
            $sis->setSisId(SisProviderEnum::ArTime);
            $sis->setName("ar-time");

            // school
            $school = new School();
            $school->setName('Archbishop Ryan High School');
            $school->setCode('arhs');
            $school->setPrivate(false);
            $school->addColor($schoolColorBase);
            $school->addColor($schoolColor1);
            $school->addColor($schoolColor2);
            $school->addEmailDomain($schoolEmailDomain1);
            $school->addEmailDomain($schoolEmailDomain2);

            // grade
            $sortPos = 0;
            for ($i = 9; $i <= 12; $i++) {
                $grade = new SchoolGrade();
                $grade->setSchool($school);
                $grade->setGrade($i);
                $grade->setSortOrder($sortPos);
                $grade->setGradeName(match ($i){
                    9  => "Freshman",
                    10 => 'Sophomore',
                    11 => 'Junior',
                    12 => 'Senior',
                });
                $grade->setHex(match ($i){
                    9  => "FFFF00",
                    10 => 'FF0000',
                    11 => 'A9A9A9',
                    12 => '000000',
                });
                $sortPos++;

                $manager->persist($grade);
            }

            // district
            $district = new District();
            $district->setName('Pilot District');
            $district->addSchool($school);

            $manager->persist($schoolColorBase);
            $manager->persist($schoolColor1);
            $manager->persist($schoolColor2);
            $manager->persist($schoolEmailDomain1);
            $manager->persist($schoolEmailDomain2);
            $manager->persist($district);
            $manager->persist($sis);
            $manager->persist($school);

            // schedule
            $schedule = new SchoolSchedule();
            $schedule->setSchool($school);
            $schedule->setExternalId('a');
            $schedule->setCode("A");
            $schedule->setIsHidden(false);
            $manager->persist($schedule);

            // config
            $schoolConfig = new SchoolConfig();
            $schoolConfig->setSchool($school);
            $schoolConfig->setDefaultSchedule($schedule);
            $schoolConfig->setEarlyDismissalThresholdMinutes(40);
            $manager->persist($schoolConfig);
        }


        loadArhs($manager);

        $manager->flush();
    }
}
