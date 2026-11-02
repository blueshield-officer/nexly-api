<?php

namespace App\Exception;

use DateTimeImmutable;
use Symfony\Component\HttpKernel\Exception\HttpException;

final class SchoolCalendarAlreadyExistsException extends HttpException
{
    public function __construct(int $schoolId, DateTimeImmutable $date)
    {
        parent::__construct(
            statusCode: 409,
            message: sprintf(
                'A calendar entry for school %d on %s already exists.',
                $schoolId,
                $date->format('Y-m-d'),
            ),
        );
    }
}
