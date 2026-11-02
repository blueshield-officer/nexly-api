<?php

namespace App\Entity;

use App\Repository\SchoolColorRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SchoolColorRepository::class)]
class SchoolColor
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?bool $base = null;

    #[ORM\Column(length: 6)]
    private ?string $hex = null;

    #[ORM\ManyToOne(inversedBy: 'colors')]
    private ?School $school = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isBase(): ?bool
    {
        return $this->base;
    }

    public function setBase(bool $base): static
    {
        $this->base = $base;

        return $this;
    }

    public function getHex(): ?string
    {
        return $this->hex;
    }

    public function setHex(string $hex): static
    {
        $this->hex = $hex;

        return $this;
    }

    public function getSchool(): ?School
    {
        return $this->school;
    }

    public function setSchool(?School $school): static
    {
        $this->school = $school;

        return $this;
    }
}
