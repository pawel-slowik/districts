<?php

declare(strict_types=1);

namespace Districts\Editor\Application\Query;

use Districts\Editor\Domain\DistrictFilter\Filter;
use Districts\Editor\Domain\DistrictOrdering;
use Districts\Editor\Domain\Pagination;

readonly class ListDistrictsQuery
{
    public function __construct(
        public DistrictOrdering $ordering,
        public Pagination $pagination,
        public ?Filter $filter,
    ) {
    }
}
