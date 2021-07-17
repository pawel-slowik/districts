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
        $this->assertSame($result->getPageSize(), 100);
        $this->assertSame($result->getTotalEntryCount(), 202);
        $this->assertSame($result->getCurrentPageEntries(), ["foo", "bar"]);
    }

    public function testPageCount(): void
    {
        $result = new PagedResult(100, 202, ["foo", "bar"]);
        $this->assertSame($result->getPageCount(), 3);
    }

    public function testCount(): void
    {
        $result = new PagedResult(100, 202, ["foo", "bar"]);
        $this->assertCount(2, $result);
    }

    public function testIterator(): void
    {
        $result = new PagedResult(100, 202, ["foo", "bar"]);
        $this->assertSame(iterator_to_array($result), ["foo", "bar"]);
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
