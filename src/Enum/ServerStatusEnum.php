<?php

namespace App\Enum;

enum ServerStatusEnum : string
{
    case Ok = "ok";
    case Maintenance = "maintenance";
    case Locked = "locked";
    case Degraded = "degraded";
}
