<?php

namespace App\Resolver;

use App\Entity\User;
use App\Enum\UserRoleEnum;

class UserRoleResolver
{
    public function resolve(User $user) : UserRoleEnum
    {
        $primaryRole = $user->getPrimaryRole();

        return match (true){
            $primaryRole === 'ROLE_DISTRICT_ADMIN' => UserRoleEnum::DistrictAdmin,
            $primaryRole === 'ROLE_DISTRICT_STAFF' => UserRoleEnum::DistrictStaff,
            $primaryRole === 'ROLE_SCHOOL_ADMIN' => UserRoleEnum::SchoolAdmin,
            $primaryRole === 'ROLE_SCHOOL_STAFF' => UserRoleEnum::SchoolStaff,
            default => UserRoleEnum::User,
        };
    }
}
