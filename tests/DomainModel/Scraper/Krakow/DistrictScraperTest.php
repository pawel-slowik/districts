<?php

declare(strict_types=1);

namespace Districts\Test\DomainModel\Scraper\Krakow;

use Districts\DomainModel\Scraper\HtmlFinder;
use Districts\DomainModel\Scraper\RuntimeException;
use Districts\DomainModel\Scraper\DistrictDTO;
use Districts\DomainModel\Scraper\Krakow\DistrictScraper;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Districts\DomainModel\Scraper\Krakow\DistrictScraper
 */
class DistrictScraperTest extends TestCase
{
    /**
     * @var DistrictScraper
     */
    private $scraper;

    /**
     * @var string
     */
    private $validHtml;

    protected function setUp(): void
    {
        $this->scraper = new DistrictScraper(new HtmlFinder());
        $this->validHtml = file_get_contents(__DIR__ . "/DzlViewGlw.jsf?id=17&lay=normal&fo=0");
    }

    public function testReturnsDistrict(): void
    {
        $district = $this->scraper->scrape($this->validHtml);
        $this->assertInstanceOf(DistrictDTO::class, $district);
    }

    public function testName(): void
    {
        $district = $this->scraper->scrape($this->validHtml);
        $this->assertSame("Wzgórza Krzesławickie", $district->getName());
    }

    public function testArea(): void
    {
        $district = $this->scraper->scrape($this->validHtml);
        $this->assertSame(23.8155, $district->getArea());
    }

    public function testPopulation(): void
    {
        $district = $this->scraper->scrape($this->validHtml);
        $this->assertSame(20205, $district->getPopulation());
    }

    public function testException(): void
    {
        $this->expectException(RuntimeException::class);
        $this->scraper->scrape("");
    }
}
