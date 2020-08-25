<?php

declare(strict_types=1);

namespace Test\Scraper\District\Gdansk;

use DomainModel\Entity\District;
use Scraper\HtmlFinder;
use Scraper\RuntimeException;
use Scraper\District\Gdansk\Builder;
use Validator\DistrictValidator;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Scraper\District\Gdansk\Builder
 */
class BuilderTest extends TestCase
{
    private $builder;

    private $validHtml;

    private $invalidAreaHtml;

    protected function setUp(): void
    {
        $this->builder = new Builder(new HtmlFinder(), new DistrictValidator());
        $this->validHtml = file_get_contents(__DIR__ . "/dzielnice_mapa_alert.php?id=16");
        $this->invalidAreaHtml = file_get_contents(__DIR__ . "/dzielnice_mapa_alert.php?id=16_invalid_area");
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
