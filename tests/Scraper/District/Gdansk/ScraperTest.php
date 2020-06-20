<?php

declare(strict_types=1);

namespace Test\Scraper\District\Gdansk;

use Entity\District;
use Scraper\HtmlFinder;
use Scraper\District\Gdansk\Scraper;
use Test\Scraper\FakeHtmlFetcher;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Scraper\District\Gdansk\Scraper
 */
class ScraperTest extends TestCase
{
    private $scraper;

    protected function setUp(): void
    {
        $this->scraper = new Scraper(new FakeHtmlFetcher(), new HtmlFinder());
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
