<?php

declare(strict_types=1);

namespace Districts\Test\Scraper\Domain\Krakow;

use Districts\Scraper\Domain\DistrictDTO;
use Districts\Scraper\Domain\Exception\ParsingException;
use Districts\Scraper\Domain\HtmlFinder;
use Districts\Scraper\Domain\Krakow\DistrictParser;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Districts\Scraper\Domain\Krakow\DistrictParser
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
        $this->assertSame("Wzgórza Krzesławickie", $district->name);
    }

    public function testArea(): void
    {
        $district = $this->districtParser->parse($this->validHtml);
        $this->assertSame(23.8155, $district->area);
    }

    public function testPopulation(): void
    {
        $district = $this->districtParser->parse($this->validHtml);
        $this->assertSame(20057, $district->population);
    }

    public function testException(): void
    {
        $this->expectException(ParsingException::class);
        $this->districtParser->parse("");
    }
}
