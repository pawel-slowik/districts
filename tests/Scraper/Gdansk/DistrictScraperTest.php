<?php

declare(strict_types=1);

namespace Districts\Test\Scraper\Gdansk;

use Districts\DomainModel\Scraper\HtmlFinder;
use Districts\DomainModel\Scraper\RuntimeException;
use Districts\DomainModel\Scraper\DistrictDTO;
use Districts\DomainModel\Scraper\Gdansk\DistrictScraper;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Districts\DomainModel\Scraper\Gdansk\DistrictScraper
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
        $this->validHtml = file_get_contents(__DIR__ . "/dzielnice_mapa_alert.php?id=16");
    }

    public function testReturnsDistrict(): void
    {
        $district = $this->scraper->scrape($this->validHtml);
        $this->assertInstanceOf(DistrictDTO::class, $district);
    }

    public function testName(): void
    {
        $district = $this->scraper->scrape($this->validHtml);
        $this->assertSame("Orunia-Św. Wojciech-Lipce", $district->getName());
    }

    public function testArea(): void
    {
        $district = $this->scraper->scrape($this->validHtml);
        $this->assertSame(19.63, $district->getArea());
    }

    public function testPopulation(): void
    {
        $district = $this->scraper->scrape($this->validHtml);
        $this->assertSame(14435, $district->getPopulation());
    }

    public function testException(): void
    {
        $this->expectException(RuntimeException::class);
        $this->scraper->scrape("");
    }
}
