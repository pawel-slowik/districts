<?php

declare(strict_types=1);

namespace Districts\Application\Query;

use Districts\DomainModel\DistrictFilter;
use Districts\DomainModel\DistrictOrdering;

final class ListDistrictsQuery
{
    private $ordering;

    private $filter;

    public function __construct(DistrictOrdering $ordering, ?DistrictFilter $filter)
    {
        $this->ordering = $ordering;
        $this->filter = $filter;
    }

    public function getOrdering(): DistrictOrdering
    {
        return $this->ordering;
    }

    public function getFilter(): ?DistrictFilter
    {
        return $this->filter;
    }
}
