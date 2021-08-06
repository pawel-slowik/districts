<?php

declare(strict_types=1);

namespace Districts\Test\DomainModel\Scraper\Krakow;

use Districts\DomainModel\Exception\ParsingException;
use Districts\DomainModel\Scraper\DistrictDTO;
use Districts\DomainModel\Scraper\HtmlFinder;
use Districts\DomainModel\Scraper\Krakow\DistrictParser;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Districts\DomainModel\Scraper\Krakow\DistrictParser
 */
class DistrictParserTest extends TestCase
{
    /**
     * @var DistrictParser
     */
    private $districtParser;

    /**
     * @var string
     */
    private $validHtml;

    protected function setUp(): void
    {
        $this->districtParser = new DistrictParser(new HtmlFinder());
        $this->validHtml = file_get_contents(__DIR__ . "/DzlViewGlw.jsf?id=17&lay=normal&fo=0");
    }

    public function testReturnsDistrict(): void
    {
        $district = $this->districtParser->parse($this->validHtml);
        $this->assertInstanceOf(DistrictDTO::class, $district);
    }

    public function testName(): void
    {
        $district = $this->districtParser->parse($this->validHtml);
        $this->assertSame("Wzgórza Krzesławickie", $district->getName());
    }

    public function testArea(): void
    {
        $district = $this->districtParser->parse($this->validHtml);
        $this->assertSame(23.8155, $district->getArea());
    }

    public function testPopulation(): void
    {
        $district = $this->districtParser->parse($this->validHtml);
        $this->assertSame(20205, $district->getPopulation());
    }

    public function testException(): void
    {
        $this->expectException(ParsingException::class);
        $this->districtParser->parse("");
    }
}
