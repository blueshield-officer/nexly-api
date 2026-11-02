<?php

namespace App\Entity;

use App\Repository\ExternalApExamRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ExternalApExamRepository::class)]
class ExternalApExam
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?ExternalApSubject $subject = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $startsAt = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $durationMinutes = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSubject(): ?ExternalApSubject
    {
        return $this->subject;
    }

    public function setSubject(?ExternalApSubject $subject): static
    {
        $this->subject = $subject;

        return $this;
    }

    public function getStartsAt(): ?\DateTimeImmutable
    {
        return $this->startsAt;
    }

    public function setStartsAt(\DateTimeImmutable $startsAt): static
    {
        $this->startsAt = $startsAt;

        return $this;
    }

    public function getDurationMinutes(): ?int
    {
        return $this->durationMinutes;
    }

    public function setDurationMinutes(int $durationMinutes): static
    {
        $this->durationMinutes = $durationMinutes;

        return $this;
    }
}
