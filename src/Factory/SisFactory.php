<?php

namespace App\Factory;

use App\Enum\SisProviderEnum;
use App\Repository\SchoolCalendarRepository;
use App\Repository\SchoolRepository;
use App\Repository\SchoolScheduleRepository;
use App\Sis\Importer\ArTimeImporter;
use App\Sis\Importer\PowerschoolImporter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

readonly class SisFactory
{
    public function __construct(
        private HttpClientInterface      $client,
        protected EntityManagerInterface $entityManager,
        protected SchoolRepository       $schoolRepository,
        private SchoolScheduleRepository $schoolScheduleRepository,
        private SchoolCalendarRepository $schoolScheduleCalendarRepository,
        private SchoolCacheFactory       $schoolCacheFactory,
    )
    {
    }

    public function create(
        int             $schoolId,
        SisProviderEnum $provider,
        string          $apiKey
    ): PowerschoolImporter|ArTimeImporter
    {
        $class = match ($provider) {
            SisProviderEnum::PowerSchool => PowerschoolImporter::class,
            SisProviderEnum::ArTime       => ArTimeImporter::class,
            SisProviderEnum::Demo         => ArTimeImporter::class,
            default => throw new \Exception('Unexpected match value'),
        };

        return new $class(
            $this->client,
            $this->entityManager,
            $this->schoolRepository,
            $this->schoolScheduleRepository,
            $this->schoolScheduleCalendarRepository,
            $this->schoolCacheFactory,
            $schoolId,
            $apiKey
        );
    }
}
