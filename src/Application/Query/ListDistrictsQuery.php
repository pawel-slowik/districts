<?php

declare(strict_types=1);

namespace Districts\Application\Query;

use Districts\DomainModel\DistrictFilter\Filter;
use Districts\DomainModel\DistrictOrdering;
use Districts\DomainModel\Pagination;

class ListDistrictsQuery
{
    public function __construct(
        private DistrictOrdering $ordering,
        private ?Filter $filter,
        private ?Pagination $pagination,
    ) {
    }

    public function getOrdering(): DistrictOrdering
    {
        return $this->ordering;
    }

    public function getFilter(): ?Filter
    {
        return $this->filter;
    }

    public function getPagination(): ?Pagination
    {
        return $this->pagination;
    }
}
