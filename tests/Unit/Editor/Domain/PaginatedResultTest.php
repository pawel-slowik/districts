<?php

declare(strict_types=1);

namespace Districts\Test\Unit\Editor\Domain;

use Districts\Editor\Domain\PaginatedResult;
use Districts\Editor\Domain\Pagination;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(PaginatedResult::class)]
class PaginatedResultTest extends TestCase
{
    public function testPageCount(): void
    {
        $result = new PaginatedResult(new Pagination(1, 100), 202, ["foo", "bar"]);
        $this->assertSame(3, $result->pageCount);
    }

    public function testExceptionOnInvalidTotalCount(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new PaginatedResult($this->createStub(Pagination::class), -1, []);
    }
}
