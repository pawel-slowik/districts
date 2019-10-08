<?php

declare(strict_types=1);

namespace Scraper;

use Entity\District;
use Scraper\City\GdanskScraper;

class GdanskScraperTest extends ScraperTestBase
{
    protected $scraper;

    protected function setUp(): void
    {
        $this->scraper = new GdanskScraper(new FakeHtmlFetcher(), new HtmlFinder());
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
