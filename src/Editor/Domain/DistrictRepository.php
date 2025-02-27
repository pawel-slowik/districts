<?php

declare(strict_types=1);

namespace Districts\Editor\Domain;

use Districts\Core\Domain\District;
use Districts\Editor\Domain\DistrictFilter\Filter;

interface DistrictRepository
{
    public function get(int $id): District;

    /**
     * @return District[]
     */
    public function list(
        DistrictOrdering $order,
        ?Filter $filter = null,
    ): array;

    /**
     * @return PaginatedResult<District>
     */
    public function listWithPagination(
        DistrictOrdering $order,
        Pagination $pagination,
        ?Filter $filter = null,
    ): PaginatedResult;
}
