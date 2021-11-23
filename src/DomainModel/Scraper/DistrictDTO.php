<?php

declare(strict_types=1);

namespace Districts\DomainModel\Scraper;

class DistrictDTO
{
    private string $name;

    private float $area;

    private int $population;

    public function __construct(string $name, float $area, int $population)
    {
        $this->name = $name;
        $this->area = $area;
        $this->population = $population;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getArea(): float
    {
        return $this->area;
    }

    public function getPopulation(): int
    {
        return $this->population;
    }
}
