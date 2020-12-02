<?php

declare(strict_types=1);

namespace Districts\Test\Scraper\District\Gdansk;

use Districts\Scraper\HtmlFinder;
use Districts\Scraper\RuntimeException;
use Districts\Scraper\District\DistrictDTO;
use Districts\Scraper\District\Gdansk\Builder;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Districts\Scraper\District\Gdansk\Builder
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
        $this->validHtml = file_get_contents(__DIR__ . "/dzielnice_mapa_alert.php?id=16");
    }

    public function testReturnsDistrict(): void
    {
        $district = $this->builder->buildFromHtml($this->validHtml);
        $this->assertInstanceOf(DistrictDTO::class, $district);
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
}
