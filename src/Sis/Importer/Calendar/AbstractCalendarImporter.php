<?php

namespace App\Sis\Importer\Calendar;

use App\Dto\SchoolDto;
use App\Sis\Formatter\Calendar\DemoCalendarFormatter;
use App\Sis\Helper\DateHelper;
use App\Sis\Storage\Calendar\SchoolCalendarStorage;
use DateTime;
use DateTimeImmutable;

abstract class AbstractCalendarImporter
{
    public function __construct(
        protected SchoolCalendarStorage $storage,
        protected DateHelper $dateHelper,

        protected DemoCalendarFormatter $formatter,
    ){}

    public abstract function import(SchoolDto $school, DateTimeImmutable $from, DateTimeImmutable $to): void;
}
