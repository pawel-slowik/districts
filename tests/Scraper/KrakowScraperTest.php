<?php

declare(strict_types=1);

namespace Test\Scraper;

use Entity\District;
use Scraper\HtmlFinder;
use Scraper\City\KrakowScraper;

class KrakowScraperTest extends ScraperTestBase
{
    protected $scraper;

    protected function setUp(): void
    {
        $this->scraper = new KrakowScraper(new FakeHtmlFetcher(), new HtmlFinder());
    }

    public function testReturnsNonEmpty(): void
    {
        $districts = $this->scraper->listDistricts();
        $this->assertNotEmpty($districts);
    }

    public function testReturnsDistricts(): void
    {
        $districts = $this->scraper->listDistricts();
        $this->assertContainsOnlyInstancesOf(District::class, $districts);
    }
}
