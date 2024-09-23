<?php

declare(strict_types=1);

namespace Districts\Scraper\Domain;

readonly class CityDTO
{
    /**
     * @param DistrictDTO[] $districts
     */
    public function __construct(
        public string $name,
        public array $districts,
    ) {
    }
}
