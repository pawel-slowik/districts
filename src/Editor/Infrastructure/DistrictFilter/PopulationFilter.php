<?php

declare(strict_types=1);

namespace Districts\Editor\Infrastructure\DistrictFilter;

use Districts\Editor\Domain\DistrictFilter\PopulationFilter as DomainPopulationFilter;

readonly class PopulationFilter extends Filter
{
    public function __construct(DomainPopulationFilter $domainFilter)
    {
        parent::__construct(
            "d.population.population >= :low AND d.population.population <= :high",
            [
                "low" => $domainFilter->begin,
                "high" => $domainFilter->end,
            ],
        );
    }
}
