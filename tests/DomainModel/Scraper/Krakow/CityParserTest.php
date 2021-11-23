<?php

declare(strict_types=1);

namespace Districts\Test\DomainModel\Scraper\Krakow;

use Districts\DomainModel\Exception\ParsingException;
use Districts\DomainModel\Scraper\HtmlFinder;
use Districts\DomainModel\Scraper\Krakow\CityParser;
use DOMElement;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Districts\DomainModel\Scraper\Krakow\CityParser
 */
class CityParserTest extends TestCase
{
    private CityParser $cityParser;

    /**
     * @var HtmlFinder|Stub
     */
    private $htmlFinder;

    protected function setUp(): void
    {
        $this->htmlFinder = $this->createStub(HtmlFinder::class);

        $this->cityParser = new CityParser($this->htmlFinder);
    }

    public function testReturnsCorrectNumberOfUrls(): void
    {
        $node = $this->createStub(DOMElement::class);
        $node
            ->method("getAttribute")
            ->with($this->identicalTo("href"))
            ->willReturn("foo");

        $this->htmlFinder
            ->method("findNodes")
            ->willReturn(array_fill(0, 5, $node));

        $urls = $this->cityParser->extractDistrictUrls("");

        $this->assertCount(5, $urls);
    }

    public function testReturnsStrings(): void
    {
        $node = $this->createStub(DOMElement::class);
        $node
            ->method("getAttribute")
            ->with($this->identicalTo("href"))
            ->willReturn("foo");

        $this->htmlFinder
            ->method("findNodes")
            ->willReturn(array_fill(0, 5, $node));

        $urls = $this->cityParser->extractDistrictUrls("");

        $this->assertContainsOnly("string", $urls, true);
    }

    public function testThrowsExceptionOnMissingNodes(): void
    {
        $this->htmlFinder
            ->method("findNodes")
            ->willReturn([]);

        $this->expectException(ParsingException::class);

        $urls = $this->cityParser->extractDistrictUrls("");
        // start the generator
        iterator_to_array($urls);
    }
}
