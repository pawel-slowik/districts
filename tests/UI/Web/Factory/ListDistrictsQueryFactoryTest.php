<?php

declare(strict_types=1);

namespace Districts\Test\UI\Web\Factory;

use Districts\UI\Web\Factory\DistrictFilterFactory;
use Districts\UI\Web\Factory\DistrictOrderingFactory;
use Districts\UI\Web\Factory\ListDistrictsQueryFactory;
use Districts\UI\Web\Factory\PaginationFactory;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * @covers \Districts\UI\Web\Factory\ListDistrictsQueryFactory
 */
class ListDistrictsQueryFactoryTest extends TestCase
{
    /**
     * @var ListDistrictsQueryFactory
     */
    private $queryFactory;

    protected function setUp(): void
    {
        $this->queryFactory = new ListDistrictsQueryFactory(
            new DistrictOrderingFactory(),
            new DistrictFilterFactory(),
            new PaginationFactory(),
        );
    }

    public function testNonEmptyQuery(): void
    {
        $routeArgs = [
            "column" => "city",
            "direction" => "asc",
        ];
        $queryParams = [
            "filterColumn" => "city",
            "filterValue" => "test",
        ];
        $request = $this->createMock(Request::class);
        $request->method("getQueryParams")->willReturn($queryParams);
        $query = $this->queryFactory->fromRequest($request, $routeArgs);
        $this->assertNotNull($query->getOrdering());
        $this->assertNotNull($query->getFilter());
        $this->assertNotNull($query->getPagination());
    }

    public function testEmptyQuery(): void
    {
        $routeArgs = [];
        $queryParams = [];
        $request = $this->createMock(Request::class);
        $request->method("getQueryParams")->willReturn($queryParams);
        $query = $this->queryFactory->fromRequest($request, $routeArgs);
        $this->assertNotNull($query->getOrdering());
        $this->assertNull($query->getFilter());
        $this->assertNotNull($query->getPagination());
    }
}
