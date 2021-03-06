<?php

declare(strict_types=1);

namespace Districts\Test\DomainModel\Scraper;

use Districts\DomainModel\Scraper\HtmlFinder;
use Districts\DomainModel\Scraper\RuntimeException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Districts\DomainModel\Scraper\HtmlFinder
 */
class HtmlFinderTest extends TestCase
{
    /**
     * @var HtmlFinder
     */
    private $finder;

    /**
     * @var string
     */
    private $validHtml;

    protected function setUp(): void
    {
        $this->finder = new HtmlFinder();
        $this->validHtml = <<<'EOT'
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
EOT;
    }

    public function testExceptionOnInvalidHtml(): void
    {
        $this->expectException(RuntimeException::class);
        $this->finder->findNodes("", "/html/body");
    }

    public function testFoundCount(): void
    {
        $nodes = $this->finder->findNodes($this->validHtml, "/html/body");
        $this->assertCount(1, $nodes);
    }

    public function testFoundType(): void
    {
        $nodes = $this->finder->findNodes($this->validHtml, "/html/body");
        $this->assertIsArray($nodes);
        $this->assertContainsOnlyInstancesOf(\DOMNode::class, $nodes);
    }

    public function testFoundValue(): void
    {
        $nodes = $this->finder->findNodes($this->validHtml, "/html/body");
        $this->assertSame("\n    test\n  ", $nodes[0]->textContent);
    }
}
