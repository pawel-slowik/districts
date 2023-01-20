<?php

declare(strict_types=1);

namespace Districts\Scraper\Domain;

class DistrictDTO
{
    public function __construct(
        private string $name,
        private float $area,
        private int $population,
    ) {
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
