<?php

namespace App\Dto\Request;

use App\Enum\SchoolCalendarDayStatusEnum;
use Symfony\Component\Validator\Constraints as Assert;
final class SchoolCalendarPatchRequest
{
    #[Assert\Uuid(versions: [Assert\Uuid::V7_MONOTONIC])]
    public ?string $scheduleUuid;

    #[Assert\Uuid(versions: [Assert\Uuid::V7_MONOTONIC])]
    public string | null $dayCycleUuid = null;

    public ?bool $schoolScheduled;

    public ?bool $dayCycleAutofill;

    #[Assert\Type('array')]
    public ?array $dayOff;
}
