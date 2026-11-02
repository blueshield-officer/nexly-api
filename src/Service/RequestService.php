<?php

namespace App\Service;

use App\Dto\BaseRequestDTO;
use App\Dto\DistrictRequestDTO;
use App\Dto\SchoolRequestDTO;
use App\Entity\DistrictRequest;
use App\Entity\Embeddable\Contact;
use App\Entity\Embeddable\Location;
use App\Entity\SchoolRequest;
use App\Entity\User;
use App\Enum\RequestStatusEnum;
use App\Repository\DistrictRequestRepository;
use App\Repository\SchoolRequestRepository;
use Doctrine\ORM\EntityManagerInterface;

readonly class RequestService
{
    public function __construct(
        private EntityManagerInterface    $em,
        private SchoolRequestRepository   $schoolRequestRepository,
        private DistrictRequestRepository $districtRequestRepository,
    ) {}
    public function hasActiveRequest(User $user): bool
    {
        return !is_null($this->schoolRequestRepository->findActive($user))
            || !is_null($this->districtRequestRepository->findActive($user));
    }

    public function findActive(User $user): BaseRequestDTO|null
    {
        $district = $this->districtRequestRepository->findActive($user);
        if ($district) return DistrictRequestDTO::fromEntity($district);

        $school = $this->schoolRequestRepository->findActive($user);
        if ($school) return SchoolRequestDTO::fromEntity($school);

        return null;
    }

    public function revoke(User $user): void
    {
        $school = $this->schoolRequestRepository->findActive($user);
        if ($school) {
            $this->em->remove($school);
            $this->em->flush();
            return;
        }

        $district = $this->districtRequestRepository->findActive($user);
        if ($district) {
            $this->em->remove($district);
            $this->em->flush();
        }
    }

    public function createSchool(User $user, SchoolRequestDTO $dto): SchoolRequest
    {
        $request = new SchoolRequest();
        $request->setUser($user);
        $request->contact = $this->buildContact($dto);
        $request->location = $this->buildLocation($dto);
        $request->setSchoolName($dto->name);

        $this->persist($request);

        return $request;
    }

    public function createDistrict(User $user, DistrictRequestDTO $dto): DistrictRequest
    {
        $request = new DistrictRequest();
        $request->setUser($user);
        $request->contact = $this->buildContact($dto);
        $request->location = $this->buildLocation($dto);
        $request->setDistrictName($dto->name);
        $request->setEstimatedSchools($dto->estimatedSchools);

        $this->persist($request);

        return $request;
    }

    private function send(string $type, string $name): void
    {
//        $this->pushoverNotifier->send(
//            message: "A new school request has been submitted for {$name}.",
//            title: "New {$type} Request",
//            sound: 'announcement'
//        );
    }

    private function persist(DistrictRequest|SchoolRequest $request): void
    {
        $this->send(
            $request instanceof SchoolRequest ? 'school' : 'district',
            $request instanceof SchoolRequest  ? $request->getSchoolName() : $request->getDistrictName()
        );

        $request->setSubmittedAt(new \DateTimeImmutable());
        $request->setStatus(RequestStatusEnum::Pending);
        $this->em->persist($request);
        $this->em->flush();
    }

    private function buildContact(BaseRequestDTO $dto): Contact
    {
        return Contact::fromArray([
            'firstName' => $dto->firstName,
            'lastName'  => $dto->lastName,
            'role'      => $dto->role,
        ]);
    }

    private function buildLocation(BaseRequestDTO $dto): Location
    {
        return Location::fromArray([
            'city'   => $dto->city,
            'state'  => $dto->state,
            'zip'    => $dto->zip,
            'street' => $dto->street,
        ]);
    }
}
