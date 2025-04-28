<?php

declare(strict_types=1);

namespace Districts\Test\Unit\Scraper\Domain\Gdansk;

use Districts\Scraper\Domain\Exception\ParsingException;
use Districts\Scraper\Domain\Gdansk\CityParser;
use Districts\Scraper\Domain\HtmlFinder;
use DOMNode;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;

#[CoversClass(CityParser::class)]
final class CityParserTest extends TestCase
{
    private CityParser $cityParser;

    private HtmlFinder&Stub $htmlFinder;

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
        $this->htmlFinder
            ->method("getAttribute")
            ->willReturnMap([[$node, "id", "0"]]);

        $urls = $this->cityParser->extractDistrictUrls("");

        $this->assertCount(5, iterator_to_array($urls));
    }

    public function testReturnsStrings(): void
    {
        $node = $this->createStub(DOMNode::class);

        $this->htmlFinder
            ->method("findNodes")
            ->willReturn(array_fill(0, 5, $node));
        $this->htmlFinder
            ->method("getAttribute")
            ->willReturnMap([[$node, "id", "0"]]);

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
        $node = $this->createStub(DOMNode::class);

        $this->htmlFinder
            ->method("findNodes")
            ->willReturn([$node]);
        $this->htmlFinder
            ->method("getAttribute")
            ->willReturn("foo");

        $this->expectException(ParsingException::class);

        $urls = $this->cityParser->extractDistrictUrls("");
        // start the generator
        iterator_to_array($urls);
    }
}
