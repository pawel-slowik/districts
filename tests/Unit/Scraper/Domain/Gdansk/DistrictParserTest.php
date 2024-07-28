<?php

declare(strict_types=1);

namespace Districts\Test\Unit\Scraper\Domain\Gdansk;

use Districts\Scraper\Domain\DistrictDTO;
use Districts\Scraper\Domain\Exception\ParsingException;
use Districts\Scraper\Domain\Gdansk\DistrictParser;
use Districts\Scraper\Domain\HtmlFinder;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Districts\Scraper\Domain\Gdansk\DistrictParser
 */
class DistrictParserTest extends TestCase
{
    private DistrictParser $districtParser;

    private string $validHtml;

    protected function setUp(): void
    {
        $this->districtParser = new DistrictParser(new HtmlFinder());
        $this->validHtml = file_get_contents(__DIR__ . "/dzielnice_mapa_alert.php?id=16");
    }

    public function testReturnsDistrict(): void
    {
        $district = $this->districtParser->parse($this->validHtml);
        $this->assertInstanceOf(DistrictDTO::class, $district);
    }

    public function testName(): void
    {
        $district = $this->districtParser->parse($this->validHtml);
        $this->assertSame("Orunia-Św. Wojciech-Lipce", $district->name);
    }

    public function testArea(): void
    {
        $district = $this->districtParser->parse($this->validHtml);
        $this->assertSame(19.63, $district->area);
    }

    public function testPopulation(): void
    {
        $district = $this->districtParser->parse($this->validHtml);
        $this->assertSame(14435, $district->population);
    }

    public function testException(): void
    {
        $this->expectException(ParsingException::class);
        $this->districtParser->parse("");
    }
}
