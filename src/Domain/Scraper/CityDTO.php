<?php

declare(strict_types=1);

namespace Districts\Domain\Scraper;

class CityDTO
{
    public function __construct(
        private string $name,
        private iterable $districts,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function listDistricts(): iterable
    {
        return $this->districts;
    }
}
