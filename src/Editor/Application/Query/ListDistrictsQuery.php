<?php

declare(strict_types=1);

namespace Districts\Editor\Application\Query;

use Districts\Editor\Domain\DistrictFilter\Filter;
use Districts\Editor\Domain\DistrictOrdering;
use Districts\Editor\Domain\Pagination;

class ListDistrictsQuery
{
    public function __construct(
        public readonly DistrictOrdering $ordering,
        public readonly ?Filter $filter,
        public readonly ?Pagination $pagination,
    ) {
    }
}
