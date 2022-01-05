<?php

declare(strict_types=1);

namespace Districts\Test\DomainModel\Scraper\Krakow;

use Districts\DomainModel\Scraper\DistrictDTO;
use Districts\DomainModel\Scraper\HtmlFetcher;
use Districts\DomainModel\Scraper\Krakow\CityParser;
use Districts\DomainModel\Scraper\Krakow\CityScraper;
use Districts\DomainModel\Scraper\Krakow\DistrictParser;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Districts\DomainModel\Scraper\Krakow\CityScraper
 */
class CityScraperTest extends TestCase
{
    private CityScraper $scraper;

    /** @var HtmlFetcher&Stub */
    private HtmlFetcher $htmlFetcher;

    /** @var CityParser&Stub */
    private CityParser $cityParser;

    /** @var DistrictParser&Stub */
    private DistrictParser $districtParser;

    protected function setUp(): void
    {
        $this->htmlFetcher = $this->createStub(HtmlFetcher::class);
        $this->cityParser = $this->createStub(CityParser::class);
        $this->districtParser = $this->createStub(DistrictParser::class);

        $this->scraper = new CityScraper(
            $this->htmlFetcher,
            $this->cityParser,
            $this->districtParser
        );
    }

    public function testCityName(): void
    {
        $this->assertSame("Kraków", $this->scraper->getCityName());
    }

    public function testScrapedCityName(): void
    {
        $cityDTO = $this->scraper->scrape();

        $this->assertSame("Kraków", $cityDTO->getName());
    }

    public function testScrapedDistrictCount(): void
    {
        $this->cityParser
            ->method("extractDistrictUrls")
            ->willReturn(["1", "2", "3"]);

        $cityDTO = $this->scraper->scrape();

        $this->assertCount(3, $cityDTO->listDistricts());
    }

    public function testScrapedDistrictType(): void
    {
        $this->cityParser
            ->method("extractDistrictUrls")
            ->willReturn(["1", "2", "3"]);

        $cityDTO = $this->scraper->scrape();

        $this->assertContainsOnlyInstancesOf(DistrictDTO::class, $cityDTO->listDistricts());
    }
}
