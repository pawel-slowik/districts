<?php

declare(strict_types=1);

namespace Districts\Application\Query;

use Districts\DomainModel\DistrictFilter;
use Districts\DomainModel\DistrictOrdering;
use Districts\DomainModel\Pagination;

final class ListDistrictsQuery
{
    private $ordering;

    private $filter;

    private $pagination;

    public function __construct(DistrictOrdering $ordering, ?DistrictFilter $filter, ?Pagination $pagination)
    {
        $this->ordering = $ordering;
        $this->filter = $filter;
        $this->pagination = $pagination;
    }

    public function getOrdering(): DistrictOrdering
    {
        return $this->ordering;
    }

    public function getFilter(): ?DistrictFilter
    {
        return $this->filter;
    }

    public function getPagination(): ?Pagination
    {
        return $this->pagination;
    }
}
