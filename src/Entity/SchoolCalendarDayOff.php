<?php

namespace App\Entity;

use App\Repository\SchoolCalendarDayOffRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SchoolCalendarDayOffRepository::class)]
class SchoolCalendarDayOff
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $title = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    /**
     * @var Collection<int, SchoolGrade>
     */
    #[Orm\JoinTable(name: 'school_calendar_day_off_link')]
    #[ORM\ManyToMany(targetEntity: SchoolGrade::class, inversedBy: 'schoolCalendarDayOffs')]
    private Collection $scope;

    public function __construct()
    {
        $this->scope = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }


    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection<int, SchoolGrade>
     */
    public function getScope(): Collection
    {
        return $this->scope;
    }

    public function addScope(SchoolGrade $scope): static
    {
        if (!$this->scope->contains($scope)) {
            $this->scope->add($scope);
        }

        return $this;
    }

    public function removeScope(SchoolGrade $scope): static
    {
        $this->scope->removeElement($scope);

        return $this;
    }

    public function isFullDayOff(): bool
    {
        return $this->scope->isEmpty();
    }
}
