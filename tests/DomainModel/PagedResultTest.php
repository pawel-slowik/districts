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

    public function testCount(): void
    {
        $result = new PagedResult(100, 202, ["foo", "bar"]);
        $this->assertCount(2, $result);
    }

    public function testIterator(): void
    {
        $result = new PagedResult(100, 202, ["foo", "bar"]);
        $this->assertSame(["foo", "bar"], iterator_to_array($result));
    }

    public function testArrayAccessExists(): void
    {
        $result = new PagedResult(100, 202, ["foo", 123 => "bar"]);
        $this->assertTrue(isset($result[123]));
    }

    public function testArrayAccessGet(): void
    {
        $result = new PagedResult(100, 202, ["foo", 123 => "bar"]);
        $this->assertSame("bar", $result[123]);
    }

    public function testArrayAccessSet(): void
    {
        $result = new PagedResult(100, 202, ["foo", 123 => "bar"]);
        $result[123] = "hello";
        $this->assertSame("hello", $result[123]);
    }

    public function testArrayAccessUnset(): void
    {
        $result = new PagedResult(100, 202, ["foo", 123 => "bar"]);
        unset($result[123]);
        $this->assertFalse(isset($result[123]));
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
