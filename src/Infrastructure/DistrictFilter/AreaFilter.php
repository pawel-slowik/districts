<?php

declare(strict_types=1);

namespace Districts\Infrastructure\DistrictFilter;

use Districts\DomainModel\DistrictFilter\AreaFilter as DomainAreaFilter;

class AreaFilter extends Filter
{
    private float $low;

    private float $high;

    public function __construct(DomainAreaFilter $domainFilter)
    {
        $this->low = $domainFilter->getBegin();
        $this->high = $domainFilter->getEnd();
    }

    public function where(): string
    {
        return "d.area.area >= :low AND d.area.area <= :high";
    }

    public function parameters(): array
    {
        return [
            "low" => $this->low,
            "high" => $this->high,
        ];
    }
}
