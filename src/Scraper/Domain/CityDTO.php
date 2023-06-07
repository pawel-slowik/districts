<?php

declare(strict_types=1);

namespace Districts\Scraper\Domain;

class CityDTO
{
    public function __construct(
        public readonly string $name,
        public readonly array $districts,
    ) {
    }
}
