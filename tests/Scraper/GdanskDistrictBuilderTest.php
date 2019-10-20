<?php

declare(strict_types=1);

namespace Scraper;

use Entity\District;
use Scraper\City\GdanskDistrictBuilder;
use Validator\DistrictValidator;

class GdanskDistrictBuilderTest extends ScraperTestBase
{
    protected $builder;

    protected $validHtml;

    protected $invalidAreaHtml;

    protected function setUp(): void
    {
        $this->builder = new GdanskDistrictBuilder(new HtmlFinder(), new DistrictValidator());
        $this->validHtml = $this->loadTestFile("Gdansk/dzielnice_mapa_alert.php?id=16");
        $this->invalidAreaHtml = $this->loadTestFile("Gdansk/dzielnice_mapa_alert.php?id=16_invalid_area");
    }

    public function testReturnsDistrict(): void
    {
        $district = $this->builder->buildFromHtml($this->validHtml);
        $this->assertInstanceOf(District::class, $district);
    }

    public function testName(): void
    {
        $district = $this->builder->buildFromHtml($this->validHtml);
        $this->assertSame("Orunia-Św. Wojciech-Lipce", $district->getName());
    }

    public function testArea(): void
    {
        $district = $this->builder->buildFromHtml($this->validHtml);
        $this->assertSame(19.63, $district->getArea());
    }

    public function testPopulation(): void
    {
        $district = $this->builder->buildFromHtml($this->validHtml);
        $this->assertSame(14435, $district->getPopulation());
    }

    public function testException(): void
    {
        $this->expectException(RuntimeException::class);
        $this->builder->buildFromHtml("");
    }

    public function testValidator(): void
    {
        $this->expectException(RuntimeException::class);
        $this->builder->buildFromHtml($this->invalidAreaHtml);
    }
}
