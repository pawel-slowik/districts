<?php

declare(strict_types=1);

namespace Districts\Test\DomainModel\Scraper\Gdansk;

use Districts\DomainModel\Scraper\DistrictDTO;
use Districts\DomainModel\Scraper\Gdansk\CityParser;
use Districts\DomainModel\Scraper\Gdansk\CityScraper;
use Districts\DomainModel\Scraper\Gdansk\DistrictParser;
use Districts\DomainModel\Scraper\HtmlFetcher;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Districts\DomainModel\Scraper\Gdansk\CityScraper
 */
class CityScraperTest extends TestCase
{
    private CityScraper $scraper;

    private HtmlFetcher $htmlFetcher;

    /**
     * @var CityParser|Stub
     */
    private $cityParser;

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
        $this->assertSame("Gdańsk", $this->scraper->getCityName());
    }

    public function testScrapedCityName(): void
    {
        $cityDTO = $this->scraper->scrape();

        $this->assertSame("Gdańsk", $cityDTO->getName());
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
