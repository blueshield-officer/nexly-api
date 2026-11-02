<?php

namespace App\Entity;

use App\Enum\SchoolCalendarDayStatusEnum;
use App\Repository\SchoolCalendarRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SchoolCalendarRepository::class)]
#[ORM\Table(
    uniqueConstraints: [
        new ORM\UniqueConstraint(
            name: 'UNIQ_SCHOOL_CALENDAR_SCHOOL_ID_DATE',
            columns: ['school_id', 'date']
        )
    ]
)]
class SchoolCalendar
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'calendar')]
    #[ORM\JoinColumn(nullable: false)]
    private ?School $school = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private ?\DateTimeImmutable $date = null;

    #[ORM\ManyToOne]
    private ?SchoolSchedule $schedule = null;

    #[ORM\ManyToOne]
    private ?SchoolDayCycle $dayCycle = null;

    #[ORM\Column(nullable: false, options: ['default' => true])]
    private bool $dayCycleAutofill;

    #[ORM\Column(nullable: false)]
    private bool $schoolScheduled = false;

    #[ORM\Column(name: "auto_change", type: Types::BOOLEAN)]
    private ?bool $autoChange = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[ORM\JoinColumn(name: 'day_off_id', nullable: true, onDelete: 'SET NULL')]
    private ?SchoolCalendarDayOff $dayOff = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeImmutable
    {
        return $this->date;
    }

    public function setDate(\DateTimeImmutable $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function isAutoChange(): ?bool
    {
        return $this->autoChange;
    }

    public function setAutoChange(bool $autoChange): static
    {
        $this->autoChange = $autoChange;

        return $this;
    }

    public function getSchool(): School
    {
        return $this->school;
    }

    public function setSchool(School $school): static
    {
        $this->school = $school;

        return $this;
    }

    public function getDayOff(): ?SchoolCalendarDayOff
    {
        return $this->dayOff;
    }

    public function setDayOff(?SchoolCalendarDayOff $dayOff): static
    {
        $this->dayOff = $dayOff;

        return $this;
    }

    public function getSchedule(): ?SchoolSchedule
    {
        return $this->schedule;
    }

    public function setSchedule(?SchoolSchedule $schedule): static
    {
        $this->schedule = $schedule;

        return $this;
    }

    public function isSchoolDay(): ?bool
    {
        return $this->schoolScheduled;
    }

    public function setSchoolScheduled(bool $schoolScheduled): static
    {
        $this->schoolScheduled = $schoolScheduled;

        return $this;
    }

    public function getStatus(): SchoolCalendarDayStatusEnum
    {
        return match (true) {
            $this->dayOff?->isFullDayOff() === true => SchoolCalendarDayStatusEnum::DAY_OFF,
            $this->schedule !== null || $this->schoolScheduled => SchoolCalendarDayStatusEnum::SCHEDULED,
            default => SchoolCalendarDayStatusEnum::REGULAR,
        };
    }

    public function getDayCycle(): ?SchoolDayCycle
    {
        return $this->dayCycle;
    }

    public function setDayCycle(?SchoolDayCycle $dayCycle): static
    {
        if ($dayCycle !== null) $this->dayCycleAutofill = false;

        $this->dayCycle = $dayCycle;

        return $this;
    }

    public function isDayCycleAutofill(): ?bool
    {
        return $this->dayCycleAutofill;
    }

    public function setDayCycleAutofill(bool $dayCycleAutofill): static
    {
        $this->dayCycleAutofill = $dayCycleAutofill;

        return $this;
    }
}
