<?php

declare(strict_types=1);

namespace Districts\Test\DomainModel;

use Districts\DomainModel\PagedResult;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Districts\DomainModel\PagedResult
 */
class PagedResultTest extends TestCase
{
    public function testGetters(): void
    {
        $result = new PagedResult(100, 202, ["foo", "bar"]);
        $this->assertSame(100, $result->getPageSize());
        $this->assertSame(202, $result->getTotalEntryCount());
        $this->assertSame(["foo", "bar"], $result->getCurrentPageEntries());
    }

    public function testPageCount(): void
    {
        $result = new PagedResult(100, 202, ["foo", "bar"]);
        $this->assertSame(3, $result->getPageCount());
    }

    public function testExceptionOnInvalidPageSize(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new PagedResult(-1, 1, []);
    }

    public function testExceptionOnInvalidTotalCount(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new PagedResult(1, -1, []);
    }
}
