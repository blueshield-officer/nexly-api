<?php

namespace App\Dto\Request;

use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

final class FeedbackBugPostRequest
{
    #[Assert\NotBlank]
    #[Assert\Uuid(versions: [Assert\Uuid::V7_MONOTONIC])]
    public Uuid $schoolUuid;

    #[Assert\NotBlank]
    #[Assert\Length(max: 1000)]
    public string $content;
}
