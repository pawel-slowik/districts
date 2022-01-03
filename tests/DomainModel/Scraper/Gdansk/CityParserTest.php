<?php

declare(strict_types=1);

namespace Districts\Test\DomainModel\Scraper\Gdansk;

use Districts\DomainModel\Scraper\Exception\ParsingException;
use Districts\DomainModel\Scraper\Gdansk\CityParser;
use Districts\DomainModel\Scraper\HtmlFinder;
use DOMElement;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Districts\DomainModel\Scraper\Gdansk\CityParser
 */
class CityParserTest extends TestCase
{
    private CityParser $cityParser;

    private HtmlFinder $htmlFinder;

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
            ->with($this->identicalTo("id"))
            ->willReturn("0");

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
            ->with($this->identicalTo("id"))
            ->willReturn("0");

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

    public function testThrowsExceptionOnInvalidId(): void
    {
        $node = $this->createStub(DOMElement::class);
        $node
            ->method("getAttribute")
            ->willReturn("foo");

        $this->htmlFinder
            ->method("findNodes")
            ->willReturn([$node]);

        $this->expectException(ParsingException::class);

        $urls = $this->cityParser->extractDistrictUrls("");
        // start the generator
        iterator_to_array($urls);
    }
}
