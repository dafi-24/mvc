<?php

namespace App\Entity;

use App\Repository\EnergyIntensityRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EnergyIntensityRepository::class)]
class EnergyIntensity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    // @phpstan-ignore-next-line
    private ?int $id = null;

    #[ORM\Column]
    private ?int $year = null;

    #[ORM\Column]
    private ?float $percentChange = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getYear(): ?int
    {
        return $this->year;
    }

    public function setYear(int $year): static
    {
        $this->year = $year;

        return $this;
    }

    public function getPercentChange(): ?float
    {
        return $this->percentChange;
    }

    public function setPercentChange(float $percentChange): static
    {
        $this->percentChange = $percentChange;

        return $this;
    }
}
