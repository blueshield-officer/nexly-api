<?php

namespace App\Repository\Interface;

use App\Entity\DistrictRequest;
use App\Entity\SchoolRequest;
use App\Entity\User;

interface RequestInterface
{
    public function findInactive(User $user) : array | null;
    public function findActive(User $user) : SchoolRequest | DistrictRequest | null;
}
