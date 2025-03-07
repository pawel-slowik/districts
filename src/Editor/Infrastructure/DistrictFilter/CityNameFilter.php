<?php

declare(strict_types=1);

namespace Districts\Editor\Infrastructure\DistrictFilter;

use Districts\Editor\Domain\DistrictFilter\CityNameFilter as DomainCityNameFilter;

readonly class CityNameFilter extends Filter
{
    private string $cityName;

    public function __construct(DomainCityNameFilter $domainFilter)
    {
        $this->cityName = $domainFilter->cityName;
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
