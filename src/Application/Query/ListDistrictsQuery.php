<?php

declare(strict_types=1);

namespace Districts\Application\Query;

use Districts\DomainModel\DistrictFilter\Filter;
use Districts\DomainModel\DistrictOrdering;
use Districts\DomainModel\Pagination;

class ListDistrictsQuery
{
    private DistrictOrdering $ordering;

    private ?Filter $filter;

    private ?Pagination $pagination;

    public function __construct(DistrictOrdering $ordering, ?Filter $filter, ?Pagination $pagination)
    {
        $this->ordering = $ordering;
        $this->filter = $filter;
        $this->pagination = $pagination;
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
