<?php

namespace App\Sis\Helper;

use DateTime;
use DateTimeImmutable;

class DateHelper
{
    public function inPast(DateTimeImmutable $date): bool
    {
        $today = new DateTimeImmutable('now', $date->getTimezone());
        if ($date->format('Y-m-d') === $today->format('Y-m-d')) return false;
        return $date < $today;
    }

    public function getDateRange(DateTimeImmutable $from, DateTimeImmutable $to): array
    {
        $dates = [];
        $cursor = DateTime::createFromImmutable($from);

        while ($cursor <= $to) {
            $dates[] = DateTimeImmutable::createFromMutable($cursor);
            $cursor->modify('+1 day');
        }

        return $dates;
    }
}
