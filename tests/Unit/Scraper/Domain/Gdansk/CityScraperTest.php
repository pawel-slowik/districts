<?php

declare(strict_types=1);

namespace Districts\Test\Unit\Scraper\Domain\Gdansk;

use Districts\Scraper\Domain\DistrictDTO;
use Districts\Scraper\Domain\Gdansk\CityParser;
use Districts\Scraper\Domain\Gdansk\CityScraper;
use Districts\Scraper\Domain\Gdansk\DistrictParser;
use Districts\Scraper\Domain\HtmlFetcher;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Districts\Scraper\Domain\Gdansk\CityScraper
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
        $this->assertSame("Gdańsk", $this->scraper->getCityName());
    }

    public function testScrapedCityName(): void
    {
        $cityDTO = $this->scraper->scrape();

        $this->assertSame("Gdańsk", $cityDTO->name);
    }

    public function testScrapedDistrictCount(): void
    {
        $this->cityParser
            ->method("extractDistrictUrls")
            ->willReturn(["1", "2", "3"]);
        $this->districtParser
            ->method("parse")
            ->willReturn(new DistrictDTO(name: "", area: 0, population: 0));

        $cityDTO = $this->scraper->scrape();

        $this->assertCount(3, $cityDTO->districts);
    }

    public function testScrapedDistrictType(): void
    {
        $this->cityParser
            ->method("extractDistrictUrls")
            ->willReturn(["1", "2", "3"]);
        $this->districtParser
            ->method("parse")
            ->willReturn(new DistrictDTO(name: "", area: 0, population: 0));

        $cityDTO = $this->scraper->scrape();

        $this->assertContainsOnlyInstancesOf(DistrictDTO::class, $cityDTO->districts);
    }
}
