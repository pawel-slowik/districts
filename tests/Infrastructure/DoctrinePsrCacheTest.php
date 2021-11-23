<?php

declare(strict_types=1);

namespace Districts\Test\Infrastructure;

use Districts\Infrastructure\DoctrinePsrCache;
use Exception;
use PHPUnit\Framework\TestCase;
use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException;

/**
 * @covers \Districts\Infrastructure\DoctrinePsrCache
 */
class DoctrinePsrCacheTest extends TestCase
{
    private DoctrinePsrCache $doctrinePsrCache;

    /**
     * @var CacheInterface
     */
    private $psrCache;

    private InvalidArgumentException $invalidArgumentException;

    protected function setUp(): void
    {
        // phpcs:ignore Squiz.WhiteSpace.ScopeClosingBrace.ContentBefore
        $this->invalidArgumentException = new class extends Exception implements InvalidArgumentException { };

        $this->psrCache = $this->createStub(CacheInterface::class);

        $this->doctrinePsrCache = new DoctrinePsrCache($this->psrCache);
    }

    public function testFetchReturnsExistingId(): void
    {
        $this->psrCache
            ->method("get")
            ->with($this->identicalTo("123"))
            ->willReturn("456");

        $result = $this->doctrinePsrCache->fetch("123");

        $this->assertSame("456", $result);
    }

    public function testFetchReturnsFalseOnMissingId(): void
    {
        $this->psrCache
            ->method("get")
            ->with($this->identicalTo("123"))
            ->will($this->returnArgument(1));

        $result = $this->doctrinePsrCache->fetch("123");

        $this->assertFalse($result);
    }

    public function testFetchReturnsFalseOnInvalidId(): void
    {
        $this->psrCache
            ->method("get")
            ->with($this->identicalTo("123"))
            ->will($this->throwException($this->invalidArgumentException));

        $result = $this->doctrinePsrCache->fetch("123");

        $this->assertFalse($result);
    }

    public function testContainsReturnsTrueOnExistingId(): void
    {
        $this->psrCache
            ->method("has")
            ->with($this->identicalTo("123"))
            ->willReturn(true);

        $result = $this->doctrinePsrCache->contains("123");

        $this->assertTrue($result);
    }

    public function testContainsReturnsFalseOnMissingId(): void
    {
        $this->psrCache
            ->method("has")
            ->with($this->identicalTo("123"))
            ->willReturn(false);

        $result = $this->doctrinePsrCache->contains("123");

        $this->assertFalse($result);
    }

    public function testContainsReturnsFalseOnInvalidId(): void
    {
        $this->psrCache
            ->method("has")
            ->with($this->identicalTo("123"))
            ->will($this->throwException($this->invalidArgumentException));

        $result = $this->doctrinePsrCache->contains("123");

        $this->assertFalse($result);
    }

    public function testSaveReturnsTrueOnSuccess(): void
    {
        $this->psrCache
            ->method("set")
            ->with($this->identicalTo("123"))
            ->willReturn(true);

        $result = $this->doctrinePsrCache->save("123", "456");

        $this->assertTrue($result);
    }

    public function testSaveReturnsFalseOnInvalidId(): void
    {
        $this->psrCache
            ->method("set")
            ->will($this->throwException($this->invalidArgumentException));

        $result = $this->doctrinePsrCache->save("123", "456");

        $this->assertFalse($result);
    }

    public function testDeleteReturnsTrueOnSuccess(): void
    {
        $this->psrCache
            ->method("delete")
            ->with($this->identicalTo("123"))
            ->willReturn(true);

        $result = $this->doctrinePsrCache->delete("123");

        $this->assertTrue($result);
    }

    public function testDeleteReturnsFalseOnInvalidId(): void
    {
        $this->psrCache
            ->method("delete")
            ->will($this->throwException($this->invalidArgumentException));

        $result = $this->doctrinePsrCache->delete("123");

        $this->assertFalse($result);
    }

    public function testStats(): void
    {
        $stats = $this->doctrinePsrCache->getStats();

        $this->assertNull($stats);
    }
}
