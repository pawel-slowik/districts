<?php

declare(strict_types=1);

namespace Districts\Test\Unit\Editor\UI\View;

use Districts\Editor\UI\View\PageReference;
use Exception;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Districts\Editor\UI\View\PageReference
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
        } catch (Exception) {
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
        } catch (Exception) {
            $exceptionThrown = true;
        }
        $this->assertFalse($exceptionThrown);
    }

    /**
     * @return array<array{0: string}>
     */
    public static function validUrlProvider(): array
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
            ["/"],
            ["path"],
            ["/path"],
            ["/path2/"],
            ["/path3/path4"],
            ["/path?query"],
            ["/path?query=foo&bar=baz"],
            ["/path#fragment"],
            ["/path?query=foo&bar=baz#fragment"],
        ];
    }

    /**
     * @dataProvider invalidUrlProvider
     */
    public function testExceptionOnInvalidUrl(string $invalidUrl): void
    {
        $this->expectException(InvalidArgumentException::class);
        new PageReference($invalidUrl, "test", false, false, false);
    }

    /**
     * @return array<array{0: string}>
     */
    public static function invalidUrlProvider(): array
    {
        return [
            [""],
            ["ssh://example.com"],
        ];
    }

    public function testExceptionOnInvalidText(): void
    {
        $this->expectException(InvalidArgumentException::class);
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
        } catch (Exception) {
            $exceptionThrown = true;
        }
        $this->assertFalse($exceptionThrown);
    }

    /**
     * @return array<array{0: bool, 1: bool, 2: bool}>
     */
    public static function validFlagsProvider(): array
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
        $this->expectException(InvalidArgumentException::class);
        new PageReference("http://example.com", "test", $isCurrent, $isPrevious, $isNext);
    }

    /**
     * @return array<array{0: bool, 1: bool, 2: bool}>
     */
    public static function invalidFlagsProvider(): array
    {
        return [
            [false, true, true],
            [true, false, true],
            [true, true, false],
            [true, true, true],
        ];
    }
}
