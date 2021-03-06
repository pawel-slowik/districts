<?php

declare(strict_types=1);

namespace Districts\Application\Command;

final class UpdateDistrictCommand
{
    private $id;

    private $name;

    private $area;

    private $population;

    public function __construct(int $id, string $name, float $area, int $population)
    {
        $this->id = $id;
        $this->name = $name;
        $this->area = $area;
        $this->population = $population;
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
