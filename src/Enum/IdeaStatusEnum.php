<?php

namespace App\Enum;

enum IdeaStatusEnum : string
{
    case Pending = 'pending';
    case InReview = 'in_review';
    case Accepted = 'accepted';
    case Rejected = 'rejected';
    case Implemented = 'implemented';
}
