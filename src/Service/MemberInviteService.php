<?php

namespace App\Service;

use App\Dto\MemberInviteDTO;
use App\Repository\DistrictMemberInviteRepository;
use App\Repository\SchoolMemberInviteRepository;
use Symfony\Component\HttpFoundation\JsonResponse;

readonly class MemberInviteService
{
    public function __construct(
        private DistrictMemberInviteRepository $districtMemberInviteRepository,
        private SchoolMemberInviteRepository $schoolMemberInviteRepository,
    ){}

    public function getActiveInvites(): array
    {
        return array_merge(
            array_map(fn($i) => MemberInviteDTO::fromSchool($i)->toArray(), $this->schoolMemberInviteRepository->getActiveInvites()),
            array_map(fn($i) => MemberInviteDTO::fromDistrict($i)->toArray(), $this->districtMemberInviteRepository->getActiveInvites()),
        );
    }
}
