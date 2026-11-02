<?php

namespace App\Enum;

enum RequestStatusEnum : string
{
    case Pending = 'pending';

    case InReview = 'in_review';

    case Approved = 'approved';

    case Rejected = 'rejected';
}
