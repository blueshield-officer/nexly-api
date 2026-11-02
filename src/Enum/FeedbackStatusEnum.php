<?php

namespace App\Enum;

enum FeedbackStatusEnum: string
{
    case Pending = 'pending';
    case InReview = 'in_review';
    case Accepted = 'accepted';
    case Rejected = 'rejected';
    case Resolved = 'resolved';
}
