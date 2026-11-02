<?php

namespace App\Scraper\Sat;

use App\Entity\ExternalSat;
use App\External\Sat\SatDto;
use App\Repository\ExternalSatRepository;
use App\Scraper\SyncResult;

readonly class SatSynchronizer
{
    public function __construct(
        private ExternalSatRepository $repository
    ) {}

    /**
     * @param array<string, SatDto> $dtos
     */
    public function sync(array $dtos): SyncResult
    {
        $result = new SyncResult();

        foreach ($dtos as $dto) {
            $entity = $this->repository->findOneBy(['testDate' => $dto->testDate]);

            if ($entity === null) {
                $entity = $this->create($dto);
                $result->created++;
            } else if ($this->hasChanges($entity, $dto)) {
                $this->update($entity, $dto);
                $result->updated++;
            } else {
                $result->skipped++;
            }

            $this->repository->persist($entity);
        }

        $this->repository->flush();

        return $result;
    }

    private function create(SatDto $dto): ExternalSat
    {
        $entity = new ExternalSat();
        $entity->setTestDate($dto->testDate);
        return $this->update($entity, $dto);
    }

    private function update(ExternalSat $entity, SatDto $dto): ExternalSat
    {
        $entity->setRegistrationDeadline($dto->registrationDeadline);
        $entity->setLateRegistrationDeadline($dto->lateRegistrationDeadline);
        $entity->setAnticipated($dto->anticipated);
        return $entity;
    }

    private function hasChanges(ExternalSat $entity, SatDto $dto): bool
    {
        return $entity->getRegistrationDeadline() != $dto->registrationDeadline
            || $entity->getLateRegistrationDeadline() != $dto->lateRegistrationDeadline
            || $entity->isAnticipated() !== $dto->anticipated;
    }
}
