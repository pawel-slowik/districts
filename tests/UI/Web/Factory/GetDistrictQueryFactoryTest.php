<?php

declare(strict_types=1);

namespace Districts\Test\UI\Web\Factory;

use Psr\Http\Message\ServerRequestInterface as Request;
use Districts\UI\Web\Factory\GetDistrictQueryFactory;
use Districts\Application\Query\GetDistrictQuery;
use Districts\Application\ValidationException;

use PHPUnit\Framework\TestCase;

/**
 * @covers \Districts\Application\Query\GetDistrictQuery
 * @covers \Districts\UI\Web\Factory\GetDistrictQueryFactory
 */
class GetDistrictQueryFactoryTest extends TestCase
{
    /**
     * @var GetDistrictQueryFactory
     */
    private $queryFactory;

    protected function setUp(): void
    {
        $this->queryFactory = new GetDistrictQueryFactory();
    }

    public function testNonEmptyQuery(): void
    {
        $request = $this->createMock(Request::class);
        $query = $this->queryFactory->fromRequest($request, ["id" => "1"]);
        $this->assertInstanceOf(GetDistrictQuery::class, $query);
        $this->assertSame(1, $query->getId());
    }

    public function testEmptyQuery(): void
    {
        $request = $this->createMock(Request::class);
        $this->expectException(ValidationException::class);
        $query = $this->queryFactory->fromRequest($request, []);
    }
}
