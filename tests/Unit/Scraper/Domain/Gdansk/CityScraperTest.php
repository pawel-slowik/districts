<?php

declare(strict_types=1);

namespace Districts\Test\Unit\Scraper\Domain\Gdansk;

use Districts\Scraper\Domain\DistrictDTO;
use Districts\Scraper\Domain\Gdansk\CityParser;
use Districts\Scraper\Domain\Gdansk\CityScraper;
use Districts\Scraper\Domain\Gdansk\DistrictParser;
use Districts\Scraper\Domain\HtmlFetcher;
use Districts\Scraper\Domain\ProgressReporter;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;

#[CoversClass(CityScraper::class)]
class CityScraperTest extends TestCase
{
    private CityScraper $scraper;

    private HtmlFetcher&Stub $htmlFetcher;

    private CityParser&Stub $cityParser;

    private DistrictParser&Stub $districtParser;

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

    public function testProgressReport(): void
    {
        $this->cityParser
            ->method("extractDistrictUrls")
            ->willReturn(["a", "b"]);

        $progressReporter = $this->createMock(ProgressReporter::class);
        $progressReporter
            ->expects($this->once())
            ->method("setTotal")
            ->with(2);
        $progressReporter
            ->expects($this->exactly(2))
            ->method("advance");

        $this->scraper->scrape($progressReporter);
    }
}
