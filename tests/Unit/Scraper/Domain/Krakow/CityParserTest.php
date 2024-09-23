<?php

declare(strict_types=1);

namespace Districts\Test\Unit\Scraper\Domain\Krakow;

use Districts\Scraper\Domain\Exception\ParsingException;
use Districts\Scraper\Domain\HtmlFinder;
use Districts\Scraper\Domain\Krakow\CityParser;
use DOMNode;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Districts\Scraper\Domain\Krakow\CityParser
 */
class CityParserTest extends TestCase
{
    private CityParser $cityParser;

    /** @var HtmlFinder&Stub */
    private HtmlFinder $htmlFinder;

    protected function setUp(): void
    {
        $this->htmlFinder = $this->createStub(HtmlFinder::class);

        $this->cityParser = new CityParser($this->htmlFinder);
    }

    public function testReturnsCorrectNumberOfUrls(): void
    {
        $node = $this->createStub(DOMNode::class);

        $this->htmlFinder
            ->method("findNodes")
            ->willReturn(array_fill(0, 5, $node));

        $urls = $this->cityParser->extractDistrictUrls("");

        $this->assertCount(5, iterator_to_array($urls));
    }

    public function testReturnsStrings(): void
    {
        $node = $this->createStub(DOMNode::class);

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
