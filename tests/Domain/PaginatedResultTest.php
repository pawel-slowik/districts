<?php

declare(strict_types=1);

namespace Districts\Test\Domain;

use Districts\Domain\PaginatedResult;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Districts\Domain\PaginatedResult
 */
class PaginatedResultTest extends TestCase
{
    public function testGetters(): void
    {
        $result = new PaginatedResult(100, 202, 333, ["foo", "bar"]);
        $this->assertSame(["foo", "bar"], $result->getCurrentPageEntries());
        $this->assertSame(333, $result->getCurrentPageNumber());
    }

    public function testPageCount(): void
    {
        $result = new PaginatedResult(100, 202, 1, ["foo", "bar"]);
        $this->assertSame(3, $result->getPageCount());
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
