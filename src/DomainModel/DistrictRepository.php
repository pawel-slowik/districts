<?php

declare(strict_types=1);

namespace Districts\DomainModel;

interface DistrictRepository
{
    public function get(int $id): District;

    public function list(
        DistrictOrdering $order,
        ?DistrictFilter $filter = null,
        ?Pagination $pagination = null
    ): PaginatedResult;
}
