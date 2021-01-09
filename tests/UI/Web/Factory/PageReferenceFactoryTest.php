<?php

declare(strict_types=1);

namespace Districts\Test\UI\Web\Factory;

use Districts\UI\Web\PageReference;
use Districts\UI\Web\Factory\PageReferenceFactory;

use PHPUnit\Framework\TestCase;

/**
 * @covers \Districts\UI\Web\Factory\PageReferenceFactory
 */
class PageReferenceFactoryTest extends TestCase
{
    /**
     * @var PageReferenceFactory
     */
    private $pageReferenceFactory;

    protected function setUp(): void
    {
        $this->pageReferenceFactory = new PageReferenceFactory();
    }

    public function testType(): void
    {
        $pageReferences = $this->pageReferenceFactory->createPageReferences("https://example.com/", 5, 1);
        $this->assertContainsOnlyInstancesOf(PageReference::class, $pageReferences);
    }

    public function testSinglePage(): void
    {
        $pageReferences = $this->pageReferenceFactory->createPageReferences("https://example.com/", 1, 1);
        $this->assertCount(0, $pageReferences);
    }

    public function testNumberOfReferences(): void
    {
        $pageReferences = $this->pageReferenceFactory->createPageReferences("https://example.com/", 100, 1);
        $this->assertCount(102, $pageReferences);
    }

    /**
     * @dataProvider flagsProvider
     */
    public function testFlags(string $baseUrl, int $pageCount, int $currentPageNumber): void
    {
        $pageReferences = $this->pageReferenceFactory->createPageReferences($baseUrl, $pageCount, $currentPageNumber);
        $materializedPageReferences = iterator_to_array($pageReferences);
        $current = [];
        foreach ($materializedPageReferences as $pageReference) {
            if ($pageReference->isCurrent()) {
                $current[] = $pageReference;
            }
        }
        $first = $materializedPageReferences[0];
        $last = $materializedPageReferences[array_key_last($materializedPageReferences)];

        $this->assertCount(1, $current);
        $this->assertTrue($first->isPrevious());
        $this->assertTrue($last->isNext());
    }

    public function flagsProvider(): array
    {
        return [
            ["https://example.com/", 10, 1],
            ["https://example.com/", 10, 5],
            ["https://example.com/", 10, 10],
        ];
    }

    public function testPreviousUrlForFirstPage(): void
    {
        $pageReferences = $this->pageReferenceFactory->createPageReferences("https://example.com/", 3, 1);
        foreach ($pageReferences as $pageReference) {
            if ($pageReference->isPrevious()) {
                $this->assertNull($pageReference->getUrl());
            }
        }
    }

    public function testNextUrlForLastPage(): void
    {
        $pageReferences = $this->pageReferenceFactory->createPageReferences("https://example.com/", 3, 3);
        foreach ($pageReferences as $pageReference) {
            if ($pageReference->isNext()) {
                $this->assertNull($pageReference->getUrl());
            }
        }
    }

    public function testUrlsForInBetweenPage(): void
    {
        $pageReferences = $this->pageReferenceFactory->createPageReferences("https://example.com/", 3, 2);
        $urls = [];
        foreach ($pageReferences as $pageReference) {
            $urls[] = $pageReference->getUrl();
        }

        $this->assertSame(
            [
                "https://example.com/?page=1",
                "https://example.com/?page=1",
                "https://example.com/?page=2",
                "https://example.com/?page=3",
                "https://example.com/?page=3",
            ],
            $urls,
        );
    }

    public function testCurrentOffset(): void
    {
        $pageReferences = $this->pageReferenceFactory->createPageReferences("https://example.com/", 20, 10);
        $currentOffset = null;
        foreach ($pageReferences as $offset => $pageReference) {
            if ($pageReference->isCurrent()) {
                $currentOffset = $offset;

                break;
            }
        }

        $this->assertSame(10, $currentOffset);
    }
}
