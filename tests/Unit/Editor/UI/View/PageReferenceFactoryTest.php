<?php

declare(strict_types=1);

namespace Districts\Test\Unit\Editor\UI\View;

use Districts\Editor\UI\View\PageReference;
use Districts\Editor\UI\View\PageReferenceFactory;
use Nyholm\Psr7\Uri;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UriInterface;

#[CoversClass(PageReferenceFactory::class)]
class PageReferenceFactoryTest extends TestCase
{
    private PageReferenceFactory $pageReferenceFactory;

    protected function setUp(): void
    {
        $this->pageReferenceFactory = new PageReferenceFactory();
    }

    public function testType(): void
    {
        $pageReferences = $this->pageReferenceFactory->createPageReferencesForUrl(
            new Uri("https://example.com/"),
            5,
            1,
        );
        $this->assertContainsOnlyInstancesOf(PageReference::class, $pageReferences);
    }

    public function testSinglePage(): void
    {
        $pageReferences = $this->pageReferenceFactory->createPageReferencesForUrl(
            new Uri("https://example.com/"),
            1,
            1,
        );
        $this->assertCount(0, iterator_to_array($pageReferences));
    }

    public function testNumberOfReferences(): void
    {
        $pageReferences = $this->pageReferenceFactory->createPageReferencesForUrl(
            new Uri("https://example.com/"),
            100,
            1,
        );
        $this->assertCount(102, iterator_to_array($pageReferences));
    }

    #[DataProvider('flagsProvider')]
    public function testFlags(UriInterface $baseUrl, int $pageCount, int $currentPageNumber): void
    {
        $pageReferences = $this->pageReferenceFactory->createPageReferencesForUrl(
            $baseUrl,
            $pageCount,
            $currentPageNumber
        );
        $materializedPageReferences = iterator_to_array($pageReferences);
        $current = [];
        foreach ($materializedPageReferences as $pageReference) {
            if ($pageReference->isCurrent) {
                $current[] = $pageReference;
            }
        }
        $first = $materializedPageReferences[0];
        $last = $materializedPageReferences[array_key_last($materializedPageReferences)];

        $this->assertCount(1, $current);
        $this->assertTrue($first->isPrevious);
        $this->assertTrue($last->isNext);
    }

    /**
     * @return array<array{0: UriInterface, 1: int, 2: int}>
     */
    public static function flagsProvider(): array
    {
        return [
            [new Uri("https://example.com/"), 10, 1],
            [new Uri("https://example.com/"), 10, 5],
            [new Uri("https://example.com/"), 10, 10],
        ];
    }

    public function testPreviousUrlForFirstPage(): void
    {
        $pageReferences = $this->pageReferenceFactory->createPageReferencesForUrl(
            new Uri("https://example.com/"),
            3,
            1,
        );
        foreach ($pageReferences as $pageReference) {
            if ($pageReference->isPrevious) {
                $this->assertNull($pageReference->url);
            }
        }
    }

    public function testNextUrlForLastPage(): void
    {
        $pageReferences = $this->pageReferenceFactory->createPageReferencesForUrl(
            new Uri("https://example.com/"),
            3,
            3,
        );
        foreach ($pageReferences as $pageReference) {
            if ($pageReference->isNext) {
                $this->assertNull($pageReference->url);
            }
        }
    }

    public function testUrlsForInBetweenPage(): void
    {
        $pageReferences = $this->pageReferenceFactory->createPageReferencesForUrl(
            new Uri("https://example.com/"),
            3,
            2,
        );
        $urls = [];
        foreach ($pageReferences as $pageReference) {
            $urls[] = $pageReference->url;
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
        $pageReferences = $this->pageReferenceFactory->createPageReferencesForUrl(
            new Uri("https://example.com/"),
            20,
            10,
        );
        $currentOffset = null;
        foreach ($pageReferences as $offset => $pageReference) {
            if ($pageReference->isCurrent) {
                $currentOffset = $offset;

                break;
            }
        }

        $this->assertSame(10, $currentOffset);
    }
}
