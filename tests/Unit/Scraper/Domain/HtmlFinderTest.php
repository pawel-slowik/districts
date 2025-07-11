<?php

declare(strict_types=1);

namespace Districts\Test\Unit\Scraper\Domain;

use Districts\Scraper\Domain\Exception\InvalidHtmlException;
use Districts\Scraper\Domain\HtmlFinder;
use DOMNode;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(HtmlFinder::class)]
final class HtmlFinderTest extends TestCase
{
    private const string VALID_HTML = <<<'HTML'
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>title</title>
  </head>
  <body>
    test
  </body>
</html>
HTML;

    private HtmlFinder $finder;

    protected function setUp(): void
    {
        $this->finder = new HtmlFinder();
    }

    public function testExceptionOnInvalidHtml(): void
    {
        $this->expectException(InvalidHtmlException::class);
        $this->finder->findNodes("", "/html/body");
    }

    public function testFoundCount(): void
    {
        $nodes = $this->finder->findNodes(self::VALID_HTML, "/html/body");
        $this->assertCount(1, $nodes);
    }

    public function testFoundType(): void
    {
        $nodes = $this->finder->findNodes(self::VALID_HTML, "/html/body");
        $this->assertContainsOnlyInstancesOf(DOMNode::class, $nodes);
    }

    public function testFoundValue(): void
    {
        $nodes = $this->finder->findNodes(self::VALID_HTML, "/html/body");
        $this->assertSame("\n    test\n  ", $nodes[0]->textContent);
    }

    public function testAttribute(): void
    {
        $nodes = $this->finder->findNodes(self::VALID_HTML, "/html/head/meta");
        $this->assertSame("utf-8", $this->finder->getAttribute($nodes[0], "charset"));
    }
}
