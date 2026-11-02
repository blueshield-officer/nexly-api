<?php

namespace App\Exception\NotFound;

use App\Enum\ErrorMessageEnum;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class DistrictNotFoundException extends NotFoundHttpException
{
    public function __construct()
    {
        parent::__construct(ErrorMessageEnum::DISTRICT_NOT_FOUND->value);
    }
}
