<?php

declare(strict_types=1);

namespace Districts\DomainModel\Scraper;

class CityDTO
{
    private string $name;

    private iterable $districts;

    public function __construct(string $name, iterable $districts)
    {
        $this->name = $name;
        $this->districts = $districts;
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
