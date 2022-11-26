<?php

declare(strict_types=1);

namespace Districts\Test\UI\CLI;

use Districts\Domain\Scraper\CityScraper;
use Districts\UI\CLI\ScraperCityFilter;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Districts\UI\CLI\ScraperCityFilter
 */
class ScraperCityFilterTest extends TestCase
{
    private CityScraper $fooCityScraper;

    private CityScraper $barCityScraper;

    /**
     * @var CityScraper[]
     */
    private array $cityScrapers;

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
        $filtered = (new ScraperCityFilter(["foo"]))->filter($this->cityScrapers);
        $this->assertContains($this->fooCityScraper, $filtered);
    }

    public function testNoMatch(): void
    {
        $filtered = (new ScraperCityFilter(["foo"]))->filter($this->cityScrapers);
        $this->assertNotContains($this->barCityScraper, $filtered);
    }

    public function testInvalid(): void
    {
        $filter = new ScraperCityFilter(["foo", "baz"]);
        $this->expectException(InvalidArgumentException::class);
        iterator_to_array($filter->filter($this->cityScrapers));
    }
}
