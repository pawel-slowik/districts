<?php

declare(strict_types=1);

namespace Districts\Editor\Infrastructure\DistrictFilter;

use Districts\Editor\Domain\DistrictFilter\NameFilter as DomainNameFilter;

readonly class NameFilter extends Filter
{
    public function __construct(DomainNameFilter $domainFilter)
    {
        parent::__construct(
            "d.name.name LIKE :search",
            [
                "search" => self::dqlLike($domainFilter->name),
            ],
        );
    }
}
