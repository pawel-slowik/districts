<?php

declare(strict_types=1);

namespace Districts\Test\UI\CLI;

use Districts\UI\CLI\ScraperCityFilter;
use Districts\Scraper\CityScraper;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Districts\UI\CLI\ScraperCityFilter
 */
class ScraperCityFilterTest extends TestCase
{
    /**
     * @var CityScraper|MockObject
     */
    private $fooCityScraper;

    /**
     * @var CityScraper|MockObject
     */
    private $barCityScraper;

    /**
     * @var (CityScraper|MockObject)[]
     */
    private $cityScrapers;

    protected function setUp(): void
    {
        $this->fooCityScraper = $this->createMock(CityScraper::class);
        $this->fooCityScraper->method("getCityName")->willReturn("foo");

        $this->barCityScraper = $this->createMock(CityScraper::class);
        $this->barCityScraper->method("getCityName")->willReturn("bar");

        $this->cityScrapers = [$this->fooCityScraper, $this->barCityScraper];
    }

    public function testMatch(): void
    {
        $filtered = (new ScraperCityFilter($this->cityScrapers, ["foo"]))->filter($this->cityScrapers);
        $this->assertContains($this->fooCityScraper, $filtered);
    }

    public function testNoMatch(): void
    {
        $filtered = (new ScraperCityFilter($this->cityScrapers, ["foo"]))->filter($this->cityScrapers);
        $this->assertNotContains($this->barCityScraper, $filtered);
    }

    public function testInvalid(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $filter = new ScraperCityFilter($this->cityScrapers, ["foo", "baz"]);
    }
}
