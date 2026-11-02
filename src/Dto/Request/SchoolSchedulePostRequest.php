<?php

namespace App\Dto\Request;

use Symfony\Component\Validator\Constraints as Assert;
final class SchoolSchedulePostRequest
{
    #[Assert\NotBlank]
    #[Assert\Length(max: 10)]
    public string $code;

    #[Assert\Length(max: 255)]
    public string $description;

    #[Assert\NotNull]
    public bool $isHidden;
}
