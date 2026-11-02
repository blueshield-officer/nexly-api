<?php

namespace App\Exception\NotFound;

use App\Enum\ErrorMessageEnum;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class SchoolScheduleNotFoundException  extends NotFoundHttpException
{
    public function __construct()
    {
        parent::__construct(ErrorMessageEnum::SCHOOL_SCHEDULE_NOT_FOUND->value);
    }
}
