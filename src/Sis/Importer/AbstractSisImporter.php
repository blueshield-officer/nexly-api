<?php

namespace App\Sis\Importer;

use App\Dto\SchoolCalendarDTO;
use App\Entity\School;
use App\Entity\SchoolCalendar;
use App\Entity\SchoolSchedule;
use App\Factory\SchoolCacheFactory;
use App\Repository\SchoolCalendarRepository;
use App\Repository\SchoolRepository;
use App\Repository\SchoolScheduleRepository;
use App\Service\Cache\SchoolCache;
use DateInterval;
use DatePeriod;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

abstract class AbstractSisImporter
{
    protected SchoolCache $schoolCache;
    protected array $response;
    /**
     * @var DateTimeImmutable[]
     */
    protected array $dates;
    protected School $school;
    protected DateTimeInterface $currentDate;

    public function __construct(
        protected HttpClientInterface               $client,
        protected readonly EntityManagerInterface   $entityManager,
        protected readonly SchoolRepository         $schoolRepository,
        protected readonly SchoolScheduleRepository $schoolScheduleRepository,
        protected readonly SchoolCalendarRepository $schoolScheduleCalendarRepository,
        protected SchoolCacheFactory                $schoolCacheFactory,
        protected int                               $schoolId,
        protected string                            $apiKey
    )
    {
        $this->schoolCache = $schoolCacheFactory->create($this->schoolId);
        $this->school = $this->schoolRepository->find($this->schoolId);
        $this->response = [];
    }

    protected function setDateInterval(
        DateTimeInterface $fromDate,
        DateTimeInterface $currentDate,
        DateTimeInterface $toDate): void
    {
        $this->dates = array_map(
            fn(DateTimeInterface $d) => DateTimeImmutable::createFromMutable($d),
            iterator_to_array(
                new DatePeriod(
                    $fromDate,
                    new DateInterval('P1D'),
                    (clone($toDate))->modify('+1 day')
                )
            )
        );
        $this->currentDate = $currentDate;
    }


    protected function getCachedDays(): array
    {
        $data = [];
        foreach ($this->dates as $index => $date) {
            $dayInfo = $this->schoolCache->getDay($date, null);
            if (!is_null($dayInfo)) {
                $data[$date->format("Y-m-d")] = $dayInfo;
                unset($this->dates[$index]);
            }
        }
        return $data;
    }

    protected function cacheDay(
        SchoolCalendarDTO | null $schoolCalendarDTO,
    )
    {
        $pastCurrentDate =
            ($this->currentDate > $schoolCalendarDTO->date) &&
            ($this->currentDate->format("Y-m-d") !== $schoolCalendarDTO->date->format("Y-m-d"));

        if ($pastCurrentDate) {
            $schoolCalendar = new SchoolCalendar();
            $schoolCalendar->setSchool($this->school);
            $schoolCalendar->setDate($schoolCalendarDTO->date);
            $schoolCalendar->setScheduleId($schoolCalendarDTO->scheduleId);;
            $schoolCalendar->setAutoChange($schoolCalendarDTO->autoChange);;
            $this->entityManager->persist($schoolCalendar);
        }

        $this->schoolCache->clearDay($schoolCalendarDTO->date);
        $this->response[$schoolCalendarDTO->date->format('Y-m-d')] = $this->schoolCache->getDay(
            $schoolCalendarDTO->date,
            $schoolCalendarDTO
        );
    }

    protected function getCachedSchedules()
    {
        return $this->schoolCache->GetSchedules();
    }

    protected function cacheSchedule(
        SchoolSchedule $schedules,
    )
    {

    }

    protected function fetchResource(string $url)
    {
        $request = $this->client->request('GET', $url);

        if ($request->getStatusCode() !== 200) $this->throw502();

        return $request->toArray();

    }
    protected function throw502()
    {
        throw new HttpException(502, "Resource which is trying to be fetched returned invalid response.");
    }
}
