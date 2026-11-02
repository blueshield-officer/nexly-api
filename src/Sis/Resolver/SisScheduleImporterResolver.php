<?php

namespace App\Sis\Resolver;

use App\Enum\SisProviderEnum;
use App\Sis\Importer\Schedule\AbstractScheduleImporter;
use App\Sis\Importer\Schedule\DemoScheduleImporter;

readonly class SisScheduleImporterResolver
{
    public function __construct(
        private DemoScheduleImporter $demoScheduleImporter
    ){}

    public function resolve(SisProviderEnum $provider): AbstractScheduleImporter
    {
        return match($provider) {
            SisProviderEnum::Demo      => $this->demoScheduleImporter,
            SisProviderEnum::PowerSchool, SisProviderEnum::ArTime => throw new \Exception('To be implemented'),
        };
    }
}
