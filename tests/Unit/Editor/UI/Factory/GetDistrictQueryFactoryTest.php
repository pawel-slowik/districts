<?php

declare(strict_types=1);

namespace Districts\Test\Unit\Editor\UI\Factory;

use Districts\Editor\UI\Factory\GetDistrictQueryFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface as Request;

#[CoversClass(GetDistrictQueryFactory::class)]
final class GetDistrictQueryFactoryTest extends TestCase
{
    private GetDistrictQueryFactory $queryFactory;

    protected function setUp(): void
    {
        $this->queryFactory = new GetDistrictQueryFactory();
    }

    public function testNonEmptyQuery(): void
    {
        $request = $this->createStub(Request::class);
        $query = $this->queryFactory->fromRoute(["id" => "1"], $request);
        $this->assertSame(1, $query->id);
    }
}
