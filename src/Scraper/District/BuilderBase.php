<?php

declare(strict_types=1);

namespace Scraper\District;

abstract class BuilderBase
{
    protected function createDistrictDTO(
        string $name,
        float $area,
        int $population
    ): DistrictDTO {
        return new DistrictDTO($name, $area, $population);
    }
}
