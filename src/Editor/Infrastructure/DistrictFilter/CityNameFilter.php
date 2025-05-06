<?php

declare(strict_types=1);

namespace Districts\Editor\Infrastructure\DistrictFilter;

use Districts\Editor\Domain\DistrictFilter\CityNameFilter as DomainCityNameFilter;

readonly class CityNameFilter extends Filter
{
    public function __construct(DomainCityNameFilter $domainFilter)
    {
        parent::__construct(
            "c.name LIKE :search",
            [
                "search" => self::dqlLike($domainFilter->cityName),
            ],
        );
    }
}
