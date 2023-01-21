<?php

declare(strict_types=1);

namespace Districts\Editor\Infrastructure\DistrictFilter;

use Districts\Domain\DistrictFilter\CityNameFilter as DomainCityNameFilter;

class CityNameFilter extends Filter
{
    private string $cityName;

    public function __construct(DomainCityNameFilter $domainFilter)
    {
        $this->cityName = $domainFilter->getCityName();
    }

    public function where(): string
    {
        return "c.name LIKE :search";
    }

    public function parameters(): array
    {
        return [
            "search" => $this->dqlLike($this->cityName),
        ];
    }
}
