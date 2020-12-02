<?php

declare(strict_types=1);

namespace Districts\Scraper\District;

class DistrictDTO
{
    private $name;

    private $area;

    private $population;

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
