<?php

declare(strict_types=1);

namespace Districts\Test\Unit\Editor\UI\View;

use Districts\Editor\UI\View\PageReference;
use Exception;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Traversable;

#[CoversClass(PageReference::class)]
class PageReferenceTest extends TestCase
{
    public function testPropertiesForPrevious(): void
    {
        $pageReference = PageReference::forPrevious("http://example.com");
        $this->assertSame("http://example.com", $pageReference->url);
        $this->assertSame("previous", $pageReference->text);
        $this->assertFalse($pageReference->isCurrent);
        $this->assertTrue($pageReference->isPrevious);
        $this->assertFalse($pageReference->isNext);
    }

    public function testPropertiesForNext(): void
    {
        $pageReference = PageReference::forNext("http://example.com");
        $this->assertSame("http://example.com", $pageReference->url);
        $this->assertSame("next", $pageReference->text);
        $this->assertFalse($pageReference->isCurrent);
        $this->assertFalse($pageReference->isPrevious);
        $this->assertTrue($pageReference->isNext);
    }

    public function testPropertiesForNumber(): void
    {
        $pageReference = PageReference::forNumber("http://example.com", 5, true);
        $this->assertSame("http://example.com", $pageReference->url);
        $this->assertSame("5", $pageReference->text);
        $this->assertTrue($pageReference->isCurrent);
        $this->assertFalse($pageReference->isPrevious);
        $this->assertFalse($pageReference->isNext);
    }

    /**
     * @param array<mixed> $args
     */
    #[DataProvider('nullUrlDataProvider')]
    public function testAcceptsNullAsUrl(callable $constructor, array $args): void
    {
        $exceptionThrown = false;
        try {
            $constructor(...$args);
        } catch (Exception) {
            $exceptionThrown = true;
        }
        $this->assertFalse($exceptionThrown);
    }

    /**
     * @return Traversable<array{0: callable, 1: array<mixed>}>
     */
    public static function nullUrlDataProvider(): Traversable
    {
        foreach (self::listConstructorsWithExtraArgs() as [$constructor, $extraArgs]) {
            yield [$constructor, array_merge([null], $extraArgs)];
        }
    }

    /**
     * @param array<mixed> $args
     */
    #[DataProvider('validUrlProvider')]
    public function testAcceptsValidUrl(callable $constructor, array $args): void
    {
        $exceptionThrown = false;
        try {
            $constructor(...$args);
        } catch (Exception) {
            $exceptionThrown = true;
        }
        $this->assertFalse($exceptionThrown);
    }

    /**
     * @return Traversable<array{0: callable, 1: array<mixed>}>
     */
    public static function validUrlProvider(): Traversable
    {
        $validUrls = [
            "https://host",
            "http://host/",
            "https://host/path",
            "http://host/path2/",
            "https://host/path3/path4",
            "http://host:1000/path",
            "https://user:password@host",
            "http://host/path?query",
            "https://host/path?query=foo&bar=baz",
            "http://host/path#fragment",
            "https://user:password@example.com/path?query=foo&bar=baz#fragment",
            "/",
            "path",
            "/path",
            "/path2/",
            "/path3/path4",
            "/path?query",
            "/path?query=foo&bar=baz",
            "/path#fragment",
            "/path?query=foo&bar=baz#fragment",
        ];
        foreach (self::listConstructorsWithExtraArgs() as [$constructor, $extraArgs]) {
            foreach ($validUrls as $validUrl) {
                yield [$constructor, array_merge([$validUrl], $extraArgs)];
            }
        }
    }

    /**
     * @param array<mixed> $args
     */
    #[DataProvider('invalidUrlProvider')]
    public function testExceptionOnInvalidUrl(callable $constructor, array $args): void
    {
        $this->expectException(InvalidArgumentException::class);
        $constructor(...$args);
    }

    /**
     * @return Traversable<array{0: callable, 1: array<mixed>}>
     */
    public static function invalidUrlProvider(): Traversable
    {
        $invalidUrls = [
            "",
            "ssh://example.com",
        ];
        foreach (self::listConstructorsWithExtraArgs() as [$constructor, $extraArgs]) {
            foreach ($invalidUrls as $invalidUrl) {
                yield [$constructor, array_merge([$invalidUrl], $extraArgs)];
            }
        }
    }

    /**
     * @return array<array{0: callable, 1: array<mixed>}>
     */
    private static function listConstructorsWithExtraArgs(): array
    {
        return [
            [PageReference::forPrevious(...), []],
            [PageReference::forNext(...), []],
            [PageReference::forNumber(...), [1, false]],
        ];
    }
}
