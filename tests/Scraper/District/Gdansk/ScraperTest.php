<?php

declare(strict_types=1);

namespace Test\Scraper\District\Gdansk;

use Scraper\HtmlFetcher;
use Scraper\HtmlFinder;
use Scraper\District\DistrictDTO;
use Scraper\District\Gdansk\Scraper;
use Test\Scraper\HtmlFetcherMockBuilder;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Scraper\District\Gdansk\Scraper
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
