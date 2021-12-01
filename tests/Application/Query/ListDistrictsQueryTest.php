<?php

declare(strict_types=1);

namespace Districts\Test\Application;

use Districts\Application\Query\ListDistrictsQuery;
use Districts\DomainModel\DistrictFilter\Filter;
use Districts\DomainModel\DistrictOrdering;
use Districts\DomainModel\Pagination;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Districts\Application\Query\ListDistrictsQuery
 */
class ListDistrictsQueryTest extends TestCase
{
    private DistrictOrdering $ordering;

    private Filter $filter;

    private Pagination $pagination;

    protected function setUp(): void
    {
        $this->ordering = $this->createStub(DistrictOrdering::class);
        $this->filter = $this->createStub(Filter::class);
        $this->pagination = $this->createStub(Pagination::class);
    }

    public function testGetters(): void
    {
        $query = new ListDistrictsQuery($this->ordering, $this->filter, $this->pagination);

        $this->assertSame($this->ordering, $query->getOrdering());
        $this->assertSame($this->filter, $query->getFilter());
        $this->assertSame($this->pagination, $query->getPagination());
    }
}
