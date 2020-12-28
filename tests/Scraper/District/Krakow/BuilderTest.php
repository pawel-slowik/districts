<?php

declare(strict_types=1);

namespace Districts\Test\Scraper\District\Krakow;

use Districts\Scraper\HtmlFinder;
use Districts\Scraper\RuntimeException;
use Districts\Scraper\DistrictDTO;
use Districts\Scraper\Krakow\Builder;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Districts\Scraper\Krakow\Builder
 */
class BuilderTest extends TestCase
{
    /**
     * @var Builder
     */
    private $builder;

    /**
     * @var string
     */
    private $validHtml;

    protected function setUp(): void
    {
        $this->builder = new Builder(new HtmlFinder());
        $this->validHtml = file_get_contents(__DIR__ . "/DzlViewGlw.jsf?id=17&lay=normal&fo=0");
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
}
