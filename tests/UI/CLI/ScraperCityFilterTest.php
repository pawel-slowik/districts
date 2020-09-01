<?php

declare(strict_types=1);

namespace Test\UI\CLI;

use UI\CLI\ScraperCityFilter;
use Scraper\District\Scraper;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \UI\CLI\ScraperCityFilter
 */
class ScraperCityFilterTest extends TestCase
{
    /**
     * @var MockObject|Scraper
     */
    private $fooScraper;

    /**
     * @var MockObject|Scraper
     */
    private $barScraper;

    /**
     * @var (MockObject|Scraper)[]
     */
    private $scrapers;

    protected function setUp(): void
    {
        $this->fooScraper = $this->createMock(Scraper::class);
        $this->fooScraper->method("getCityName")->willReturn("foo");

        $this->barScraper = $this->createMock(Scraper::class);
        $this->barScraper->method("getCityName")->willReturn("bar");

        $this->scrapers = [$this->fooScraper, $this->barScraper];
    }

    public function testMatch(): void
    {
        $filtered = (new ScraperCityFilter($this->scrapers, ["foo"]))->filter($this->scrapers);
        $this->assertContains($this->fooScraper, $filtered);
    }

    public function testNoMatch(): void
    {
        $filtered = (new ScraperCityFilter($this->scrapers, ["foo"]))->filter($this->scrapers);
        $this->assertNotContains($this->barScraper, $filtered);
    }

    public function testInvalid(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $filter = new ScraperCityFilter($this->scrapers, ["foo", "baz"]);
    }
}
