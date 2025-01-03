<?php

declare(strict_types=1);

namespace Districts\Test\Unit\Editor\UI\Factory;

use Districts\Editor\Application\Exception\ValidationException;
use Districts\Editor\UI\Factory\GetDistrictQueryFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface as Request;

#[CoversClass(GetDistrictQueryFactory::class)]
class GetDistrictQueryFactoryTest extends TestCase
{
    private GetDistrictQueryFactory $queryFactory;

    protected function setUp(): void
    {
        $this->queryFactory = new GetDistrictQueryFactory();
    }

    public function testNonEmptyQuery(): void
    {
        $request = $this->createMock(Request::class);
        $query = $this->queryFactory->fromRequest($request, ["id" => "1"]);
        $this->assertSame(1, $query->id);
    }

    public function testEmptyQuery(): void
    {
        $request = $this->createMock(Request::class);
        $this->expectException(ValidationException::class);
        $this->queryFactory->fromRequest($request, []);
    }
}
