<?php

namespace App\Enum;

enum ServerStatusScopeEnum : string
{
    case Global = "global";
    case Service = "service";
}
