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
    private $scrapers;

    protected function setUp(): void
    {
        $scraper = $this->createMock(DistrictScraper::class);
        $scraper->method("getCityName")->willReturn("foo");
        $this->scrapers = [$scraper];
    }

    public function testMatch(): void
    {
        $filter = new ScraperCityFilter($this->scrapers, ["foo"]);
        $this->assertTrue($filter->matches("foo"));
    }

    public function testNoMatch(): void
    {
        $filter = new ScraperCityFilter($this->scrapers, ["foo"]);
        $this->assertFalse($filter->matches("bar"));
    }

    public function testInvalid(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $filter = new ScraperCityFilter($this->scrapers, ["foo", "baz"]);
    }
}
