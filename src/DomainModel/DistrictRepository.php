<?php

declare(strict_types=1);

namespace Districts\DomainModel;

use Districts\DomainModel\DistrictFilter\Filter;

interface DistrictRepository
{
    public function get(int $id): District;

    public function list(
        DistrictOrdering $order,
        ?Filter $filter = null,
        ?Pagination $pagination = null
    ): PaginatedResult;
}
