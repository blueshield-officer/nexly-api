<?php

namespace App\Sis\Resolver;

use App\Enum\SisProviderEnum;
use App\Sis\Importer\Calendar\AbstractCalendarImporter;
use App\Sis\Importer\Calendar\DemoCalendarImporter;

readonly class SisCalendarImporterResolver
{
    public function __construct(
        private DemoCalendarImporter $demoScheduleImporter
    ){}

    public function resolve(SisProviderEnum $provider): AbstractCalendarImporter
    {
        return match ($provider) {
            SisProviderEnum::Demo => $this->demoScheduleImporter,
            SisProviderEnum::ArTime, SisProviderEnum::PowerSchool => throw new \Exception('To be implemented'),
        };
    }
}
