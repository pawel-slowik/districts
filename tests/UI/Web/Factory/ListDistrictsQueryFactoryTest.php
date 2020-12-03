<?php

declare(strict_types=1);

namespace Districts\Test\UI\Web\Factory;

use Psr\Http\Message\ServerRequestInterface as Request;
use Districts\UI\Web\Factory\ListDistrictsQueryFactory;
use Districts\UI\Web\Factory\DistrictOrderingFactory;
use Districts\UI\Web\Factory\DistrictFilterFactory;
use Districts\Application\Query\ListDistrictsQuery;

use PHPUnit\Framework\TestCase;

/**
 * @covers \Districts\Application\Query\ListDistrictsQuery
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
        $this->assertInstanceOf(ListDistrictsQuery::class, $query);
        $this->assertNotNull($query->getOrdering());
        $this->assertNotNull($query->getFilter());
    }

    public function testEmptyQuery(): void
    {
        $routeArgs = [];
        $queryParams = [];
        $request = $this->createMock(Request::class);
        $request->method("getQueryParams")->willReturn($queryParams);
        $query = $this->queryFactory->fromRequest($request, $routeArgs);
        $this->assertInstanceOf(ListDistrictsQuery::class, $query);
        $this->assertNotNull($query->getOrdering());
        $this->assertNull($query->getFilter());
    }
}
