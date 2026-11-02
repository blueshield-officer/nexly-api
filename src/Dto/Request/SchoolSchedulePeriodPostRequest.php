<?php

namespace App\Dto\Request;

use App\Validator\Constraints\HhMmTime;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

final class SchoolSchedulePeriodPostRequest
{
    #[Assert\NotBlank]
    #[Assert\Uuid(versions: [Assert\Uuid::V7_MONOTONIC])]
    public Uuid $periodUuid;

    #[Assert\NotBlank]
    #[HhMmTime]
    public string $startTime;

    #[Assert\NotBlank]
    #[HhMmTime]
    public string $endTime;
}
