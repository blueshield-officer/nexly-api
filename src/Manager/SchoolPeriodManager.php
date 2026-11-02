<?php

namespace App\Manager;

use App\Dto\Request\SchoolPeriodPatchRequest;
use App\Dto\Request\Util\PatchFieldChecker;
use App\Entity\School;
use App\Entity\SchoolPeriod;
use App\Entity\SchoolSchedulePeriod;
use App\Sis\Storage\Schedule\SchoolPeriodStorage;
use Doctrine\ORM\EntityManagerInterface;

final readonly class SchoolPeriodManager
{
    public function __construct(
        private EntityManagerInterface $em,
        private SchoolPeriodStorage    $storage,
    ) {}

    public function create(int $schoolId, string $name): SchoolPeriod
    {
        $schoolRef = $this->em->getReference(School::class, $schoolId);

        $period = new SchoolPeriod();
        $period->setSchool($schoolRef);
        $period->setName(trim($name));

        $this->em->persist($period);
        $this->em->flush();

        $this->storage->invalidate($schoolId);

        return $period;
    }

    public function patch(int $schoolId, SchoolPeriod $period, SchoolPeriodPatchRequest $request): SchoolPeriod
    {
        $changed = false;

        if (PatchFieldChecker::isProvided($request, 'name')) {
            $name = trim($request->name);

            if ($name !== $period->getName()) {
                $period->setName($name);
                $changed = true;
            }
        }

        if (!$changed) {
            return $period;
        }

        $this->em->flush();
        $this->storage->invalidate($schoolId);

        return $period;
    }

    public function remove(int $schoolId, SchoolPeriod $period): void
    {
        $this->em->wrapInTransaction(function () use ($period) {
            $schedulePeriods = $this->em->getRepository(SchoolSchedulePeriod::class)
                ->findBy(['period' => $period]);

            foreach ($schedulePeriods as $sp) {
                $this->em->remove($sp);
            }

            $this->em->remove($period);
            $this->em->flush();
        });

        $this->storage->invalidate($schoolId);
    }
}
