<?php

declare(strict_types=1);

namespace Districts\Test\Unit\Scraper\Application;

use Districts\Scraper\Application\ScraperCollection;
use Districts\Scraper\Domain\CityScraper;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;

#[CoversClass(ScraperCollection::class)]
final class ScraperCollectionTest extends TestCase
{
    private CityScraper&Stub $scraperFoo;

    private CityScraper&Stub $scraperBar;

    private ScraperCollection $scraperCollection;

    protected function setUp(): void
    {
        $this->scraperFoo = $this->createStub(CityScraper::class);
        $this->scraperFoo->method("getCityName")->willReturn("foo");
        $this->scraperBar = $this->createStub(CityScraper::class);
        $this->scraperBar->method("getCityName")->willReturn("Bar");
        $this->scraperCollection = new ScraperCollection(
            $this->scraperFoo,
            $this->scraperBar,
        );
    }

    public function testReturnsAllOnEmptyFilter(): void
    {
        $filtered = $this->scraperCollection->filterByCityNames([]);
        $this->assertSame([$this->scraperFoo, $this->scraperBar], $filtered);
    }

    public function testReturnsFiltered(): void
    {
        $filtered = $this->scraperCollection->filterByCityNames(["Bar"]);
        $this->assertSame([$this->scraperBar], $filtered);
    }

    public function testThrowsExceptionOnUnsupportedName(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->scraperCollection->filterByCityNames(["bar"]);
    }
}
