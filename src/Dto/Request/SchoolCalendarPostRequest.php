<?php

namespace App\Dto\Request;
use Symfony\Component\Validator\Constraints as Assert;

final class SchoolCalendarPostRequest
{
    #[Assert\Uuid(versions: [Assert\Uuid::V7_MONOTONIC])]
    public string | null $scheduleUuid = null;

    #[Assert\Uuid(versions: [Assert\Uuid::V7_MONOTONIC])]
    public string | null $dayCycleUuid = null;
    public ?bool $schoolScheduled;

    public ?bool $dayCycleAutofill;

    public array | null $dayOff = null;
}
