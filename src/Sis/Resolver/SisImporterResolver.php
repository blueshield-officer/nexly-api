<?php

namespace App\Sis\Resolver;

use App\Factory\SisFactory;
use App\Repository\SchoolRepository;
use App\Service\Cache\GlobalCache;
use App\Sis\Contract\SisImporterResolverInterface;
use App\Sis\Importer\ArTimeImporter;
use App\Sis\Importer\PowerschoolImporter;

readonly class SisImporterResolver implements SisImporterResolverInterface
{
    public function __construct(
        private SchoolRepository $schoolRepository,
        private SisFactory       $sisFactory,
        private GlobalCache      $globalCache,
    )
    {}

    // TODO extend with those secrets and etc            ||
    //                                                   V
    public function resolve(
        string $schoolUuid,
        string $apiKey
    ) : ArTimeImporter | PowerschoolImporter
    {
        $schoolId = $this->globalCache->getSchoolId(
            $schoolUuid,
            $this->schoolRepository
        );

        $sis = $this->schoolRepository->find($schoolId)->getSis();

        return $this->sisFactory->create($schoolId, $sis->getSisId(), $apiKey);
    }
}
