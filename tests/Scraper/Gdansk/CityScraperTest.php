<?php

declare(strict_types=1);

namespace Districts\Test\Scraper\Gdansk;

use Districts\Scraper\HtmlFetcher;
use Districts\Scraper\HtmlFinder;
use Districts\Scraper\DistrictDTO;
use Districts\Scraper\Gdansk\CityScraper;
use Districts\Test\Scraper\HtmlFetcherMockBuilder;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Districts\Scraper\Gdansk\CityScraper
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
        $urlFilenameMap = ["https://www.gdansk.pl/dzielnice" => "dzielnice.html"];
        for ($i = 1; $i <= 35; $i++) {
            $url = sprintf("https://www.gdansk.pl/subpages/dzielnice/html/dzielnice_mapa_alert.php?id=%d", $i);
            $urlFilenameMap[$url] = "dzielnice_mapa_alert.php?id=16";
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
