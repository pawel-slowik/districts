<?php

declare(strict_types=1);

namespace Districts\Editor\Infrastructure\DistrictFilter;

use Districts\Editor\Domain\DistrictFilter\AreaFilter as DomainAreaFilter;

readonly class AreaFilter extends Filter
{
    public function __construct(DomainAreaFilter $domainFilter)
    {
        parent::__construct(
            "d.area.area >= :low AND d.area.area <= :high",
            [
                "low" => $domainFilter->begin,
                "high" => $domainFilter->end,
            ],
        );
    }
}
