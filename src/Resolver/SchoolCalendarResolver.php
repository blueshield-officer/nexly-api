<?php

namespace App\Resolver;

use App\Dto\SchoolDto;
use App\Sis\Storage\Calendar\SchoolCalendarStorage;
use DateTimeImmutable;
use DateTimeZone;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

readonly class SchoolCalendarResolver
{
    public function __construct(
        private SchoolCalendarStorage $storage,
    ) {}
    public function resolve(
        SchoolDto $school, ?string $from, ?string $to,
    ) : array
    {
        if (is_null($from) || is_null($to)) throw new BadRequestHttpException("Missing Date Parameters. Please set \"from\" and \"to\" date.");

        $timezone = new DateTimeZone($school->timezone);

        $fromDate = DateTimeImmutable::createFromFormat('Y-m-d', $from, $timezone);
        $toDate = DateTimeImmutable::createFromFormat('Y-m-d', $to, $timezone);

        if (!$fromDate || !$toDate) throw new BadRequestHttpException("Invalid Date Parameters. Please set \"from\" and \"to\" date at Y-m-d format.");

        if ($fromDate > $toDate) throw new BadRequestHttpException("Invalid Date Parameters. \"from\" date cannot be greater than \"to\" date.");

        $diff = $fromDate->diff($toDate)->days;
        if ($diff > 100) throw new BadRequestHttpException('Date range cannot exceed 100 days.');

//        if (!is_null($school->sis)){
//            $importer = $importerResolver->resolve($school->sis);
//            $importer->import($school, $fromDate, $toDate);
//        }

        return $this->storage->getMany($school->id, $fromDate, $toDate);
    }
}
