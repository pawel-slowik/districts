<?php

declare(strict_types=1);

namespace Test\Scraper\District\Krakow;

use Scraper\HtmlFinder;
use Scraper\RuntimeException;
use Scraper\District\DistrictDTO;
use Scraper\District\Krakow\Builder;
use Validator\DistrictValidator;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Scraper\District\Krakow\Builder
 */
class BuilderTest extends TestCase
{
    private $builder;

    private $validHtml;

    private $invalidPopulationHtml;

    protected function setUp(): void
    {
        $this->builder = new Builder(new HtmlFinder(), new DistrictValidator());
        $this->validHtml = file_get_contents(__DIR__ . "/DzlViewGlw.jsf?id=17&lay=normal&fo=0");
        $this->invalidPopulationHtml = file_get_contents(
            __DIR__ . "/DzlViewGlw.jsf?id=17&lay=normal&fo=0_invalid_population"
        );
    }

    public function testReturnsDistrict(): void
    {
        $district = $this->builder->buildFromHtml($this->validHtml);
        $this->assertInstanceOf(DistrictDTO::class, $district);
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

    public function testValidator(): void
    {
        $this->expectException(RuntimeException::class);
        $this->builder->buildFromHtml($this->invalidPopulationHtml);
    }
}
