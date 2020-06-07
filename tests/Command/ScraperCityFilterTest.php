<?php

declare(strict_types=1);

namespace Test\Command;

use Command\ScraperCityFilter;
use Scraper\DistrictScraper;

use PHPUnit\Framework\TestCase;

/**
 * @covers \Command\ScraperCityFilter
 */
class ScraperCityFilterTest extends TestCase
{
    private $fooScraper;

    private $barScraper;

    private $scrapers;

    protected function setUp(): void
    {
        $this->fooScraper = $this->createMock(DistrictScraper::class);
        $this->fooScraper->method("getCityName")->willReturn("foo");

        $this->barScraper = $this->createMock(DistrictScraper::class);
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
