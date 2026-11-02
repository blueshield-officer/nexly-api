<?php

namespace App\Manager;

use App\Dto\Request\SchoolDayCyclePatchRequest;
use App\Dto\Request\SchoolDayCyclePostRequest;
use App\Dto\Request\Util\PatchFieldChecker;
use App\Entity\School;
use App\Entity\SchoolDayCycle;
use App\Repository\SchoolDayCycleRepository;
use App\Sis\Storage\DayCycle\SchoolDayCycleStorage;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManagerInterface;

readonly final class SchoolDayCycleManager
{
    public function __construct(
        private EntityManagerInterface $em,
        private SchoolDayCycleRepository $repository,
        private SchoolDayCycleStorage $storage,
    ) {}
    public function create(int $schoolId, SchoolDayCyclePostRequest $request) : array
    {
        $this->em->wrapInTransaction(function () use ($schoolId, $request) {
            $schoolRef = $this->em->getReference(School::class, $schoolId);

            $highestPosition = $this->repository->findHighestPositionBySchoolId($schoolId);
            $nextPosition = $highestPosition !== null ? $highestPosition + 1 : 0;
            $position =  (PatchFieldChecker::isProvided($request, 'position')) ?
                min($request->position, $nextPosition)
                : $nextPosition;

            if ($position < $nextPosition) {
                $this->repository->shiftPositions($schoolId, $position, null, +1);
            }

            $schoolDayCycle = new SchoolDayCycle();
            $schoolDayCycle->setSchool($schoolRef);
            $schoolDayCycle->setPosition($position);
            $schoolDayCycle->setCode($request->code);


            $this->em->persist($schoolDayCycle);
            $this->em->flush();
        });

        $this->storage->invalidate($schoolId);

        return $this->storage->get($schoolId);
    }

    public function update(int $schoolId, SchoolDayCycle $schoolDayCycle, SchoolDayCyclePatchRequest $request) : array
    {
        $changed = false;

        if (PatchFieldChecker::isProvided($request, 'code') && $schoolDayCycle->getCode()  !== $request->code) {
            $schoolDayCycle->setCode($request->code);
            $changed = true;
        }

        if (PatchFieldChecker::isProvided($request, 'position')) {
            $this->em->wrapInTransaction(function () use ($schoolId, $schoolDayCycle, $request, &$changed) {
                // TODO do more on pessimistic write lock
                $this->em->find(School::class, $schoolId, LockMode::PESSIMISTIC_WRITE);

                $old = $schoolDayCycle->getPosition();
                $highest = $this->repository->findHighestPositionBySchoolId($schoolId);
                $new = min($request->position, $highest);

                if ($new > $old) {
                    $this->repository->shiftPositions($schoolId, $old + 1, $new, -1);
                } elseif ($new < $old) {
                    $this->repository->shiftPositions($schoolId, $new, $old - 1, +1);
                }

                if ($new !== $old) {
                    $schoolDayCycle->setPosition($new);
                    $changed = true;
                }

                $this->em->flush();
            });
        } elseif ($changed) {
            $this->em->flush();
        }

        if ($changed) {
            $this->storage->invalidate($schoolId);
        }

        return $this->storage->get($schoolId);
    }
    public function remove(int $schoolId, SchoolDayCycle $schoolDayCycle) : array
    {
        $this->em->wrapInTransaction(function () use ($schoolId, $schoolDayCycle) {
            $this->em->find(School::class, $schoolId, LockMode::PESSIMISTIC_WRITE);

            $position = $schoolDayCycle->getPosition();

            $this->em->remove($schoolDayCycle);
            $this->em->flush();

            $this->repository->shiftPositions($schoolId, $position, null, -1);
        });

        $this->storage->invalidate($schoolId);

        return $this->storage->get($schoolId);
    }
}
