<?php

declare(strict_types=1);

namespace Districts\Scraper\Domain;

class DistrictDTO
{
    public function __construct(
        public readonly string $name,
        public readonly float $area,
        public readonly int $population,
    ) {
    }
}
