<?php

namespace App\Enum;

enum UserRoleEnum : string
{
    case DistrictAdmin = 'districtAdmin';
    case DistrictStaff = 'districtStaff';
    case SchoolAdmin = 'schoolAdmin';
    case SchoolStaff = 'schoolStaff';

    case User = 'user';
}
