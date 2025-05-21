<?php

namespace App\Entity;

use App\Repository\EnergyShareRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EnergyShareRepository::class)]
#[ORM\Table(name: 'energy_share')]
class EnergyShare
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    // @phpstan-ignore-next-line
    private ?int $id = null;

    #[ORM\Column(type: 'integer')]
    private int $year;

    #[ORM\Column(type: 'float')]
    private float $heatingIndustry;

    #[ORM\Column(type: 'float')]
    private float $electricity;

    #[ORM\Column(type: 'float')]
    private float $transport;

    #[ORM\Column(type: 'float')]
    private float $total;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getYear(): int
    {
        return $this->year;
    }

    public function setYear(int $year): static
    {
        $this->year = $year;
        return $this;
    }

    public function getHeatingIndustry(): float
    {
        return $this->heatingIndustry;
    }

    public function setHeatingIndustry(float $value): static
    {
        $this->heatingIndustry = $value;
        return $this;
    }

    public function getElectricity(): float
    {
        return $this->electricity;
    }

    public function setElectricity(float $value): static
    {
        $this->electricity = $value;
        return $this;
    }

    public function getTransport(): float
    {
        return $this->transport;
    }

    public function setTransport(float $value): static
    {
        $this->transport = $value;
        return $this;
    }

    public function getTotal(): float
    {
        return $this->total;
    }

    public function setTotal(float $value): static
    {
        $this->total = $value;
        return $this;
    }
}
