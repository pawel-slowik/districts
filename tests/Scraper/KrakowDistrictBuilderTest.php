<?php

declare(strict_types=1);

namespace Scraper;

use Entity\District;
use Scraper\City\KrakowDistrictBuilder;

class KrakowDistrictBuilderTest extends ScraperTestBase
{
    protected $builder;

    protected $validHtml;

    protected function setUp(): void
    {
        $this->builder = new KrakowDistrictBuilder(new HtmlFinder());
        $this->validHtml = $this->loadTestFile("Krakow/DzlViewGlw.jsf?id=17&lay=normal&fo=0");
    }

    public function testReturnsDistrict(): void
    {
        $district = $this->builder->buildFromHtml($this->validHtml);
        $this->assertInstanceOf(District::class, $district);
    }

    public function testName(): void
    {
        $district = $this->builder->buildFromHtml($this->validHtml);
        $this->assertSame("Wzgórza Krzesławickie", $district->getName());
    }

    public function testArea(): void
    {
        $district = $this->builder->buildFromHtml($this->validHtml);
        $this->assertSame(23.8155, $district->getArea());
    }

    public function testPopulation(): void
    {
        $district = $this->builder->buildFromHtml($this->validHtml);
        $this->assertSame(20205, $district->getPopulation());
    }

    public function testException(): void
    {
        $this->expectException(RuntimeException::class);
        $this->builder->buildFromHtml("");
    }
}
