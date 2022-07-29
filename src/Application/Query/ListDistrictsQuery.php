<?php

declare(strict_types=1);

namespace Districts\Application\Query;

use Districts\DomainModel\DistrictFilter\Filter;
use Districts\DomainModel\DistrictOrdering;
use Districts\DomainModel\Pagination;

class ListDistrictsQuery
{
    public function __construct(
        public readonly DistrictOrdering $ordering,
        public readonly ?Filter $filter,
        public readonly ?Pagination $pagination,
    ) {
    }
}
