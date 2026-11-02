<?php

namespace App\Enum;

enum ServerModuleEnum : string
{
    case All = "all";

    case Api = "api";
    case Payment = "payment";
    case Auth = "auth";
}
