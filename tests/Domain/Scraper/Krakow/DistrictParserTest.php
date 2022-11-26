<?php

declare(strict_types=1);

namespace Districts\Test\Domain\Scraper\Krakow;

use Districts\Domain\Scraper\DistrictDTO;
use Districts\Domain\Scraper\Exception\ParsingException;
use Districts\Domain\Scraper\HtmlFinder;
use Districts\Domain\Scraper\Krakow\DistrictParser;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Districts\Domain\Scraper\Krakow\DistrictParser
 */
class DistrictParserTest extends TestCase
{
    private DistrictParser $districtParser;

    private string $validHtml;

    protected function setUp(): void
    {
        $this->districtParser = new DistrictParser(new HtmlFinder());
        $this->validHtml = file_get_contents(__DIR__ . "/dzielnica_xvii.html");
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
        $this->assertSame(20057, $district->getPopulation());
    }

    public function testException(): void
    {
        $this->expectException(ParsingException::class);
        $this->districtParser->parse("");
    }
}
