<?php

declare(strict_types=1);

namespace Districts\Editor\Infrastructure\DistrictFilter;

use Districts\Editor\Domain\DistrictFilter\AreaFilter as DomainAreaFilter;

readonly class AreaFilter extends Filter
{
    private float $low;

    private float $high;

    public function __construct(DomainAreaFilter $domainFilter)
    {
        $this->low = $domainFilter->begin;
        $this->high = $domainFilter->end;
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
