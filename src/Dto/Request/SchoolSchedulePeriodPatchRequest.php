<?php

namespace App\Dto\Request;

use App\Validator\Constraints\HhMmTime;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;
final class SchoolSchedulePeriodPatchRequest
{
    #[Assert\Uuid(versions: [Assert\Uuid::V7_MONOTONIC])]
    public ?Uuid $periodUuid;

    #[HhMmTime]
    public ?string $startTime;

    #[HhMmTime]
    public ?string $endTime;
}
