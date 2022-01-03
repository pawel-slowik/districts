<?php

declare(strict_types=1);

namespace Districts\Application\Command;

class UpdateDistrictCommand
{
    public function __construct(
        private int $id,
        private string $name,
        private float $area,
        private int $population,
    ) {
    }

    public function getId(): int
    {
        return $this->id;
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
