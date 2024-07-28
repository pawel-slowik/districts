<?php

declare(strict_types=1);

namespace Districts\Scraper\Domain;

readonly class DistrictDTO
{
    public function __construct(
        public string $name,
        public float $area,
        public int $population,
    ) {
    }
}
