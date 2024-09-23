<?php

declare(strict_types=1);

namespace Districts\Editor\Domain;

use Districts\Editor\Domain\DistrictFilter\Filter;

interface DistrictRepository
{
    public function get(int $id): District;

    /**
     * @return PaginatedResult<District>
     */
    public function list(
        DistrictOrdering $order,
        ?Filter $filter = null,
        ?Pagination $pagination = null
    ): PaginatedResult;
}
