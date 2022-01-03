<?php

declare(strict_types=1);

namespace Districts\Application\Command;

class AddDistrictCommand
{
    public function __construct(
        private int $cityId,
        private string $name,
        private float $area,
        private int $population,
    ) {
    }

    public function getCityId(): int
    {
        return $this->cityId;
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
