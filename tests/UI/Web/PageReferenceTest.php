<?php

declare(strict_types=1);

namespace Districts\Test\UI\Web;

use Districts\UI\Web\PageReference;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Districts\UI\Web\PageReference
 */
class PageReferenceTest extends TestCase
{
    public function testGetters(): void
    {
        $pageReference = new PageReference("http://example.com", "test", false, true, false);
        $this->assertSame("http://example.com", $pageReference->getUrl());
        $this->assertSame("test", $pageReference->getText());
        $this->assertFalse($pageReference->isCurrent());
        $this->assertTrue($pageReference->isPrevious());
        $this->assertFalse($pageReference->isNext());
    }

    public function testAcceptsNullAsUrl(): void
    {
        $exceptionThrown = false;
        try {
            new PageReference(null, "test", false, false, false);
        } catch (\Exception $exception) {
            $exceptionThrown = true;
        }
        $this->assertFalse($exceptionThrown);
    }

    /**
     * @dataProvider validUrlProvider
     */
    public function testAcceptsValidUrl(string $validUrl): void
    {
        $exceptionThrown = false;
        try {
            new PageReference($validUrl, "test", false, false, false);
        } catch (\Exception $exception) {
            $exceptionThrown = true;
        }
        $this->assertFalse($exceptionThrown);
    }

    public function validUrlProvider(): array
    {
        return [
            ["https://host"],
            ["http://host/"],
            ["https://host/path"],
            ["http://host/path2/"],
            ["https://host/path3/path4"],
            ["http://host:1000/path"],
            ["https://user:password@host"],
            ["http://host/path?query"],
            ["https://host/path?query=foo&bar=baz"],
            ["http://host/path#fragment"],
            ["https://user:password@example.com/path?query=foo&bar=baz#fragment"],
            // TODO: add support for relative URLs
            // ["/"],
            // ["path"],
            // ["/path"],
            // ["/path2/"],
            // ["/path3/path4"],
            // ["/path?query"],
            // ["/path?query=foo&bar=baz"],
            // ["/path#fragment"],
            // ["/path?query=foo&bar=baz#fragment"],
        ];
    }

    /**
     * @dataProvider invalidUrlProvider
     */
    public function testExceptionOnInvalidUrl(string $invalidUrl): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new PageReference($invalidUrl, "test", false, false, false);
    }

    public function invalidUrlProvider(): array
    {
        return [
            [""],
            ["ssh://example.com"],
        ];
    }

    public function testExceptionOnInvalidText(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new PageReference("http://example.com", "", false, false, false);
    }

    /**
     * @dataProvider validFlagsProvider
     */
    public function testAcceptsValidFlagCombinations(bool $isCurrent, bool $isPrevious, bool $isNext): void
    {
        $exceptionThrown = false;
        try {
            new PageReference("http://example.com", "test", $isCurrent, $isPrevious, $isNext);
        } catch (\Exception $exception) {
            $exceptionThrown = true;
        }
        $this->assertFalse($exceptionThrown);
    }

    public function validFlagsProvider(): array
    {
        return [
            [false, false, false],
            [false, false, true],
            [false, true, false],
            [true, false, false],
        ];
    }

    /**
     * @dataProvider invalidFlagsProvider
     */
    public function testExceptionOnInvalidFlagCombinations(bool $isCurrent, bool $isPrevious, bool $isNext): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new PageReference("http://example.com", "test", $isCurrent, $isPrevious, $isNext);
    }

    public function invalidFlagsProvider(): array
    {
        return [
            [false, true, true],
            [true, false, true],
            [true, true, false],
            [true, true, true],
        ];
    }
}
