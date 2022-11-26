<?php

declare(strict_types=1);

namespace Districts\Application\Query;

use Districts\Domain\DistrictFilter\Filter;
use Districts\Domain\DistrictOrdering;
use Districts\Domain\Pagination;

class ListDistrictsQuery
{
    public function __construct(
        public readonly DistrictOrdering $ordering,
        public readonly ?Filter $filter,
        public readonly ?Pagination $pagination,
    ) {
    }
}
