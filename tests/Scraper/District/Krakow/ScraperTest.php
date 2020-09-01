<?php

declare(strict_types=1);

namespace Test\Scraper\District\Krakow;

use Scraper\HtmlFinder;
use Scraper\HtmlFetcher;
use Scraper\District\DistrictDTO;
use Scraper\District\Krakow\Scraper;
use Test\Scraper\HtmlFetcherMockBuilder;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Scraper\District\Krakow\Scraper
 */
class ScraperTest extends TestCase
{
    /**
     * @var Scraper
     */
    private $scraper;

    protected function setUp(): void
    {
        $this->scraper = new Scraper($this->createHtmlFetcherMock(), new HtmlFinder());
    }

    public function testReturnsNonEmpty(): void
    {
        $districts = $this->scraper->listDistricts();
        $this->assertNotEmpty($districts);
    }

    public function testReturnsDistricts(): void
    {
        $districts = $this->scraper->listDistricts();
        $this->assertContainsOnlyInstancesOf(DistrictDTO::class, $districts);
    }

    private function createHtmlFetcherMock(): HtmlFetcher
    {
        // The mock returns the same content for all districts. This is OK
        // because Scrapers don't have any knowledge about district properties
        // (only Builders do).
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
