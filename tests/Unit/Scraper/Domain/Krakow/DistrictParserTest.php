<?php

declare(strict_types=1);

namespace Districts\Test\Unit\Scraper\Domain\Krakow;

use Districts\Scraper\Domain\Exception\ParsingException;
use Districts\Scraper\Domain\HtmlFinder;
use Districts\Scraper\Domain\Krakow\DistrictParser;
use LogicException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(DistrictParser::class)]
class DistrictParserTest extends TestCase
{
    private DistrictParser $districtParser;

    private string $validHtml;

    protected function setUp(): void
    {
        $this->districtParser = new DistrictParser(new HtmlFinder());
        $validHtml = file_get_contents(__DIR__ . "/dzielnica_xvii.html") or throw new LogicException();
        $this->validHtml = $validHtml;
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
        $this->assertSame(19892, $district->population);
    }

    public function testException(): void
    {
        $this->expectException(ParsingException::class);
        $this->districtParser->parse("");
    }
}
