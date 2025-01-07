<?php

declare(strict_types=1);

namespace Districts\Test\Unit\Editor\Domain;

use Districts\Editor\Domain\PaginatedResult;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(PaginatedResult::class)]
class PaginatedResultTest extends TestCase
{
    public function testPageCount(): void
    {
        $result = new PaginatedResult(100, 202, 1, ["foo", "bar"]);
        $this->assertSame(3, $result->pageCount);
    }

    public function testExceptionOnInvalidPageSize(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new PaginatedResult(-1, 1, 1, []);
    }

    public function testExceptionOnInvalidTotalCount(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new PaginatedResult(1, -1, 1, []);
    }

    public function testExceptionOnInvalidPageNumber(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new PaginatedResult(1, 1, 0, []);
    }
}
