<?php

declare(strict_types=1);

namespace Districts\Application\Command;

class AddDistrictCommand
{
    private $cityId;

    private $name;

    private $area;

    private $population;

    public function __construct(int $cityId, string $name, float $area, int $population)
    {
        $this->cityId = $cityId;
        $this->name = $name;
        $this->area = $area;
        $this->population = $population;
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
