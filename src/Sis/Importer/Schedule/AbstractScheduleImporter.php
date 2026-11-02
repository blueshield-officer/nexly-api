<?php

namespace App\Sis\Importer\Schedule;

abstract class AbstractScheduleImporter
{
    abstract public function import(): array;
}
