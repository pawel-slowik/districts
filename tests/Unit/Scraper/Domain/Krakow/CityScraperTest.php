<?php

declare(strict_types=1);

namespace Districts\Test\Unit\Scraper\Domain\Krakow;

use Districts\Scraper\Domain\DistrictDTO;
use Districts\Scraper\Domain\HtmlFetcher;
use Districts\Scraper\Domain\Krakow\CityParser;
use Districts\Scraper\Domain\Krakow\CityScraper;
use Districts\Scraper\Domain\Krakow\DistrictParser;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Districts\Scraper\Domain\Krakow\CityScraper
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

        $this->assertSame("Kraków", $cityDTO->name);
    }

    public function testScrapedDistrictCount(): void
    {
        $this->cityParser
            ->method("extractDistrictUrls")
            ->willReturn(["1", "2", "3"]);

        $cityDTO = $this->scraper->scrape();

        $this->assertCount(3, $cityDTO->districts);
    }

    public function testScrapedDistrictType(): void
    {
        $this->cityParser
            ->method("extractDistrictUrls")
            ->willReturn(["1", "2", "3"]);

        $cityDTO = $this->scraper->scrape();

        $this->assertContainsOnlyInstancesOf(DistrictDTO::class, $cityDTO->districts);
    }
}
