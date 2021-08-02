<?php

declare(strict_types=1);

namespace Districts\Test\DomainModel\Scraper\Gdansk;

use Districts\DomainModel\Scraper\Gdansk\CityParser;
use Districts\DomainModel\Scraper\HtmlFinder;
use Districts\DomainModel\Scraper\RuntimeException;
use DOMElement;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Districts\DomainModel\Scraper\Gdansk\CityParser
 */
class CityParserTest extends TestCase
{
    /**
     * @var CityParser
     */
    private $cityParser;

    /**
     * @var HtmlFinder|Stub
     */
    private $htmlFinder;

    protected function setUp(): void
    {
        $this->htmlFinder = $this->createStub(HtmlFinder::class);

        $this->parser = new CityParser($this->htmlFinder);
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

        $urls = $this->parser->extractDistrictUrls("");

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

        $urls = $this->parser->extractDistrictUrls("");

        $this->assertContainsOnly("string", $urls, true);
    }

    public function testThrowsExceptionOnMissingNodes(): void
    {
        $this->htmlFinder
            ->method("findNodes")
            ->willReturn([]);

        $this->expectException(RuntimeException::class);

        $urls = $this->parser->extractDistrictUrls("");
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

        $this->expectException(RuntimeException::class);

        $urls = $this->parser->extractDistrictUrls("");
        // start the generator
        iterator_to_array($urls);
    }
}
