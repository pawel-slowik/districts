<?php

declare(strict_types=1);

namespace Districts\Editor\Infrastructure\DistrictFilter;

use Districts\Domain\DistrictFilter\PopulationFilter as DomainPopulationFilter;

class PopulationFilter extends Filter
{
    private int $low;

    private int $high;

    public function __construct(DomainPopulationFilter $domainFilter)
    {
        $this->low = $domainFilter->getBegin();
        $this->high = $domainFilter->getEnd();
    }

    public function where(): string
    {
        return "d.population.population >= :low AND d.population.population <= :high";
    }

    public function parameters(): array
    {
        return [
            "low" => $this->low,
            "high" => $this->high,
        ];
    }
}
