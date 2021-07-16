<?php

declare(strict_types=1);

namespace Districts\Test\DomainModel\Scraper\Krakow;

use Districts\DomainModel\Scraper\DistrictDTO;
use Districts\DomainModel\Scraper\HtmlFetcher;
use Districts\DomainModel\Scraper\HtmlFinder;
use Districts\DomainModel\Scraper\Krakow\CityScraper;
use Districts\Test\DomainModel\Scraper\HtmlFetcherMockBuilder;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Districts\DomainModel\Scraper\Krakow\CityScraper
 */
class CityScraperTest extends TestCase
{
    /**
     * @var CityScraper
     */
    private $scraper;

    protected function setUp(): void
    {
        $this->scraper = new CityScraper($this->createHtmlFetcherMock(), new HtmlFinder());
    }

    public function testReturnsNonEmpty(): void
    {
        $cityDTO = $this->scraper->scrape();
        $this->assertNotEmpty($cityDTO->listDistricts());
    }

    public function testReturnsDistricts(): void
    {
        $cityDTO = $this->scraper->scrape();
        $this->assertContainsOnlyInstancesOf(DistrictDTO::class, $cityDTO->listDistricts());
    }

    private function createHtmlFetcherMock(): HtmlFetcher
    {
        // The mock returns the same content for all districts. This is OK
        // because CityScrapers don't have any knowledge about district
        // properties (only DistrictScrapers do).
        $urlFilenameMap = [
            // phpcs:ignore Generic.Files.LineLength.TooLong
            "http://appimeri.um.krakow.pl/app-pub-dzl/pages/DzlViewAll.jsf?a=1&lay=normal&fo=0" => "DzlViewAll.jsf?a=1&lay=normal&fo=0",
        ];
        for ($i = 1; $i <= 18; $i++) {
            $url = sprintf("http://appimeri.um.krakow.pl/app-pub-dzl/pages/DzlViewGlw.jsf?id=%d&lay=normal&fo=0", $i);
            $urlFilenameMap[$url] = "DzlViewGlw.jsf?id=17&lay=normal&fo=0";
        }
        return HtmlFetcherMockBuilder::buildFromUrlFilenameMap(
            $this->createMock(HtmlFetcher::class),
            array_map(
                function ($filename) {
                    return __DIR__ . "/" . $filename;
                },
                $urlFilenameMap,
            ),
        );
    }
}
